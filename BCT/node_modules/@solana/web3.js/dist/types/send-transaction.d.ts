import type { GetAccountInfoApi, GetEpochInfoApi, GetSignatureStatusesApi, Rpc, SendTransactionApi } from '@solana/rpc';
import type { AccountNotificationsApi, RpcSubscriptions, SignatureNotificationsApi, SlotNotificationsApi } from '@solana/rpc-subscriptions';
import { BaseTransaction, IDurableNonceTransaction, IFullySignedTransaction, ITransactionWithBlockhashLifetime, ITransactionWithFeePayer } from '@solana/transactions';
import { SendableTransaction, sendAndConfirmDurableNonceTransaction_INTERNAL_ONLY_DO_NOT_EXPORT, sendAndConfirmTransactionWithBlockhashLifetime_INTERNAL_ONLY_DO_NOT_EXPORT, sendTransaction_INTERNAL_ONLY_DO_NOT_EXPORT } from './send-transaction-internal.js';
interface SendAndConfirmDurableNonceTransactionFactoryConfig {
    rpc: Rpc<GetAccountInfoApi & GetSignatureStatusesApi & SendTransactionApi>;
    rpcSubscriptions: RpcSubscriptions<AccountNotificationsApi & SignatureNotificationsApi>;
}
interface SendAndConfirmTransactionWithBlockhashLifetimeFactoryConfig {
    rpc: Rpc<GetEpochInfoApi & GetSignatureStatusesApi & SendTransactionApi>;
    rpcSubscriptions: RpcSubscriptions<SignatureNotificationsApi & SlotNotificationsApi>;
}
interface SendTransactionWithoutConfirmingFactoryConfig {
    rpc: Rpc<SendTransactionApi>;
}
type SendAndConfirmTransactionWithBlockhashLifetimeFunction = (transaction: ITransactionWithBlockhashLifetime & SendableTransaction, config: Omit<Parameters<typeof sendAndConfirmTransactionWithBlockhashLifetime_INTERNAL_ONLY_DO_NOT_EXPORT>[0], 'confirmRecentTransaction' | 'rpc' | 'transaction'>) => Promise<void>;
type SendAndConfirmDurableNonceTransactionFunction = (transaction: BaseTransaction & IDurableNonceTransaction & IFullySignedTransaction & ITransactionWithFeePayer, config: Omit<Parameters<typeof sendAndConfirmDurableNonceTransaction_INTERNAL_ONLY_DO_NOT_EXPORT>[0], 'confirmDurableNonceTransaction' | 'rpc' | 'transaction'>) => Promise<void>;
type SendTransactionWithoutConfirmingFunction = (transaction: SendableTransaction, config: Omit<Parameters<typeof sendTransaction_INTERNAL_ONLY_DO_NOT_EXPORT>[0], 'rpc' | 'transaction'>) => Promise<void>;
export declare function sendTransactionWithoutConfirmingFactory({ rpc, }: SendTransactionWithoutConfirmingFactoryConfig): SendTransactionWithoutConfirmingFunction;
export declare function sendAndConfirmDurableNonceTransactionFactory({ rpc, rpcSubscriptions, }: SendAndConfirmDurableNonceTransactionFactoryConfig): SendAndConfirmDurableNonceTransactionFunction;
export declare function sendAndConfirmTransactionFactory({ rpc, rpcSubscriptions, }: SendAndConfirmTransactionWithBlockhashLifetimeFactoryConfig): SendAndConfirmTransactionWithBlockhashLifetimeFunction;
export {};
//# sourceMappingURL=send-transaction.d.ts.map