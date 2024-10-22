'use strict';

var accounts = require('@solana/accounts');
var addresses = require('@solana/addresses');
var codecs = require('@solana/codecs');
var errors = require('@solana/errors');
var functional = require('@solana/functional');
var instructions = require('@solana/instructions');
var keys = require('@solana/keys');
var programs = require('@solana/programs');
var rpc = require('@solana/rpc');
var rpcParsedTypes = require('@solana/rpc-parsed-types');
var rpcSubscriptions = require('@solana/rpc-subscriptions');
var rpcTypes = require('@solana/rpc-types');
var signers = require('@solana/signers');
var transactions = require('@solana/transactions');
var transactionConfirmation = require('@solana/transaction-confirmation');

// src/index.ts

// src/airdrop-internal.ts
async function requestAndConfirmAirdrop_INTERNAL_ONLY_DO_NOT_EXPORT({
  abortSignal,
  commitment,
  confirmSignatureOnlyTransaction,
  lamports,
  recipientAddress,
  rpc
}) {
  const airdropTransactionSignature = await rpc.requestAirdrop(recipientAddress, lamports, { commitment }).send({ abortSignal });
  await confirmSignatureOnlyTransaction({
    abortSignal,
    commitment,
    signature: airdropTransactionSignature
  });
  return airdropTransactionSignature;
}

// src/airdrop.ts
function airdropFactory({ rpc, rpcSubscriptions }) {
  const getRecentSignatureConfirmationPromise = transactionConfirmation.createRecentSignatureConfirmationPromiseFactory(
    rpc,
    rpcSubscriptions
  );
  async function confirmSignatureOnlyTransaction(config) {
    await transactionConfirmation.waitForRecentTransactionConfirmationUntilTimeout({
      ...config,
      getRecentSignatureConfirmationPromise,
      getTimeoutPromise: transactionConfirmation.getTimeoutPromise
    });
  }
  return async function airdrop(config) {
    return await requestAndConfirmAirdrop_INTERNAL_ONLY_DO_NOT_EXPORT({
      ...config,
      confirmSignatureOnlyTransaction,
      rpc
    });
  };
}
var compiledTransactionDecoder = void 0;
async function fetchLookupTables(lookupTableAddresses, rpc, config) {
  const fetchedLookupTables = await accounts.fetchJsonParsedAccounts(
    rpc,
    lookupTableAddresses,
    config
  );
  accounts.assertAccountsDecoded(fetchedLookupTables);
  accounts.assertAccountsExist(fetchedLookupTables);
  return fetchedLookupTables.reduce((acc, lookup) => {
    return {
      ...acc,
      [lookup.address]: lookup.data.addresses
    };
  }, {});
}
async function decodeTransaction(encodedTransaction, rpc, config) {
  const { lastValidBlockHeight, ...fetchAccountsConfig } = config ?? {};
  if (!compiledTransactionDecoder)
    compiledTransactionDecoder = transactions.getCompiledTransactionDecoder();
  const compiledTransaction = compiledTransactionDecoder.decode(encodedTransaction);
  const { compiledMessage } = compiledTransaction;
  const lookupTables = "addressTableLookups" in compiledMessage && compiledMessage.addressTableLookups !== void 0 && compiledMessage.addressTableLookups.length > 0 ? compiledMessage.addressTableLookups : [];
  const lookupTableAddresses = lookupTables.map((l) => l.lookupTableAddress);
  const fetchedLookupTables = lookupTableAddresses.length > 0 ? await fetchLookupTables(lookupTableAddresses, rpc, fetchAccountsConfig) : {};
  return transactions.decompileTransaction(compiledTransaction, {
    addressesByLookupTableAddress: fetchedLookupTables,
    lastValidBlockHeight
  });
}
function getSendTransactionConfigWithAdjustedPreflightCommitment(commitment, config) {
  if (
    // The developer has supplied no value for `preflightCommitment`.
    !config?.preflightCommitment && // The value of `commitment` is lower than the server default of `preflightCommitment`.
    rpcTypes.commitmentComparator(
      commitment,
      "finalized"
      /* default value of `preflightCommitment` */
    ) < 0
  ) {
    return {
      ...config,
      // In the common case, it is unlikely that you want to simulate a transaction at
      // `finalized` commitment when your standard of commitment for confirming the
      // transaction is lower. Cap the simulation commitment level to the level of the
      // confirmation commitment.
      preflightCommitment: commitment
    };
  }
  return config;
}
async function sendTransaction_INTERNAL_ONLY_DO_NOT_EXPORT({
  abortSignal,
  commitment,
  rpc,
  transaction,
  ...sendTransactionConfig
}) {
  const base64EncodedWireTransaction = transactions.getBase64EncodedWireTransaction(transaction);
  return await rpc.sendTransaction(base64EncodedWireTransaction, {
    ...getSendTransactionConfigWithAdjustedPreflightCommitment(commitment, sendTransactionConfig),
    encoding: "base64"
  }).send({ abortSignal });
}
async function sendAndConfirmDurableNonceTransaction_INTERNAL_ONLY_DO_NOT_EXPORT({
  abortSignal,
  commitment,
  confirmDurableNonceTransaction,
  rpc,
  transaction,
  ...sendTransactionConfig
}) {
  const transactionSignature = await sendTransaction_INTERNAL_ONLY_DO_NOT_EXPORT({
    ...sendTransactionConfig,
    abortSignal,
    commitment,
    rpc,
    transaction
  });
  await confirmDurableNonceTransaction({
    abortSignal,
    commitment,
    transaction
  });
  return transactionSignature;
}
async function sendAndConfirmTransactionWithBlockhashLifetime_INTERNAL_ONLY_DO_NOT_EXPORT({
  abortSignal,
  commitment,
  confirmRecentTransaction,
  rpc,
  transaction,
  ...sendTransactionConfig
}) {
  const transactionSignature = await sendTransaction_INTERNAL_ONLY_DO_NOT_EXPORT({
    ...sendTransactionConfig,
    abortSignal,
    commitment,
    rpc,
    transaction
  });
  await confirmRecentTransaction({
    abortSignal,
    commitment,
    transaction
  });
  return transactionSignature;
}

// src/send-transaction.ts
function sendTransactionWithoutConfirmingFactory({
  rpc
}) {
  return async function sendTransactionWithoutConfirming(transaction, config) {
    await sendTransaction_INTERNAL_ONLY_DO_NOT_EXPORT({
      ...config,
      rpc,
      transaction
    });
  };
}
function sendAndConfirmDurableNonceTransactionFactory({
  rpc,
  rpcSubscriptions
}) {
  const getNonceInvalidationPromise = transactionConfirmation.createNonceInvalidationPromiseFactory(rpc, rpcSubscriptions);
  const getRecentSignatureConfirmationPromise = transactionConfirmation.createRecentSignatureConfirmationPromiseFactory(
    rpc,
    rpcSubscriptions
  );
  async function confirmDurableNonceTransaction(config) {
    await transactionConfirmation.waitForDurableNonceTransactionConfirmation({
      ...config,
      getNonceInvalidationPromise,
      getRecentSignatureConfirmationPromise
    });
  }
  return async function sendAndConfirmDurableNonceTransaction(transaction, config) {
    await sendAndConfirmDurableNonceTransaction_INTERNAL_ONLY_DO_NOT_EXPORT({
      ...config,
      confirmDurableNonceTransaction,
      rpc,
      transaction
    });
  };
}
function sendAndConfirmTransactionFactory({
  rpc,
  rpcSubscriptions
}) {
  const getBlockHeightExceedencePromise = transactionConfirmation.createBlockHeightExceedencePromiseFactory({
    rpc,
    rpcSubscriptions
  });
  const getRecentSignatureConfirmationPromise = transactionConfirmation.createRecentSignatureConfirmationPromiseFactory(
    rpc,
    rpcSubscriptions
  );
  async function confirmRecentTransaction(config) {
    await transactionConfirmation.waitForRecentTransactionConfirmation({
      ...config,
      getBlockHeightExceedencePromise,
      getRecentSignatureConfirmationPromise
    });
  }
  return async function sendAndConfirmTransaction(transaction, config) {
    await sendAndConfirmTransactionWithBlockhashLifetime_INTERNAL_ONLY_DO_NOT_EXPORT({
      ...config,
      confirmRecentTransaction,
      rpc,
      transaction
    });
  };
}

exports.airdropFactory = airdropFactory;
exports.decodeTransaction = decodeTransaction;
exports.sendAndConfirmDurableNonceTransactionFactory = sendAndConfirmDurableNonceTransactionFactory;
exports.sendAndConfirmTransactionFactory = sendAndConfirmTransactionFactory;
exports.sendTransactionWithoutConfirmingFactory = sendTransactionWithoutConfirmingFactory;
Object.keys(accounts).forEach(function (k) {
  if (k !== 'default' && !Object.prototype.hasOwnProperty.call(exports, k)) Object.defineProperty(exports, k, {
    enumerable: true,
    get: function () { return accounts[k]; }
  });
});
Object.keys(addresses).forEach(function (k) {
  if (k !== 'default' && !Object.prototype.hasOwnProperty.call(exports, k)) Object.defineProperty(exports, k, {
    enumerable: true,
    get: function () { return addresses[k]; }
  });
});
Object.keys(codecs).forEach(function (k) {
  if (k !== 'default' && !Object.prototype.hasOwnProperty.call(exports, k)) Object.defineProperty(exports, k, {
    enumerable: true,
    get: function () { return codecs[k]; }
  });
});
Object.keys(errors).forEach(function (k) {
  if (k !== 'default' && !Object.prototype.hasOwnProperty.call(exports, k)) Object.defineProperty(exports, k, {
    enumerable: true,
    get: function () { return errors[k]; }
  });
});
Object.keys(functional).forEach(function (k) {
  if (k !== 'default' && !Object.prototype.hasOwnProperty.call(exports, k)) Object.defineProperty(exports, k, {
    enumerable: true,
    get: function () { return functional[k]; }
  });
});
Object.keys(instructions).forEach(function (k) {
  if (k !== 'default' && !Object.prototype.hasOwnProperty.call(exports, k)) Object.defineProperty(exports, k, {
    enumerable: true,
    get: function () { return instructions[k]; }
  });
});
Object.keys(keys).forEach(function (k) {
  if (k !== 'default' && !Object.prototype.hasOwnProperty.call(exports, k)) Object.defineProperty(exports, k, {
    enumerable: true,
    get: function () { return keys[k]; }
  });
});
Object.keys(programs).forEach(function (k) {
  if (k !== 'default' && !Object.prototype.hasOwnProperty.call(exports, k)) Object.defineProperty(exports, k, {
    enumerable: true,
    get: function () { return programs[k]; }
  });
});
Object.keys(rpc).forEach(function (k) {
  if (k !== 'default' && !Object.prototype.hasOwnProperty.call(exports, k)) Object.defineProperty(exports, k, {
    enumerable: true,
    get: function () { return rpc[k]; }
  });
});
Object.keys(rpcParsedTypes).forEach(function (k) {
  if (k !== 'default' && !Object.prototype.hasOwnProperty.call(exports, k)) Object.defineProperty(exports, k, {
    enumerable: true,
    get: function () { return rpcParsedTypes[k]; }
  });
});
Object.keys(rpcSubscriptions).forEach(function (k) {
  if (k !== 'default' && !Object.prototype.hasOwnProperty.call(exports, k)) Object.defineProperty(exports, k, {
    enumerable: true,
    get: function () { return rpcSubscriptions[k]; }
  });
});
Object.keys(rpcTypes).forEach(function (k) {
  if (k !== 'default' && !Object.prototype.hasOwnProperty.call(exports, k)) Object.defineProperty(exports, k, {
    enumerable: true,
    get: function () { return rpcTypes[k]; }
  });
});
Object.keys(signers).forEach(function (k) {
  if (k !== 'default' && !Object.prototype.hasOwnProperty.call(exports, k)) Object.defineProperty(exports, k, {
    enumerable: true,
    get: function () { return signers[k]; }
  });
});
Object.keys(transactions).forEach(function (k) {
  if (k !== 'default' && !Object.prototype.hasOwnProperty.call(exports, k)) Object.defineProperty(exports, k, {
    enumerable: true,
    get: function () { return transactions[k]; }
  });
});
//# sourceMappingURL=out.js.map
//# sourceMappingURL=index.browser.cjs.map