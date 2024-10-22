import { type FetchAccountsConfig } from '@solana/accounts';
import type { GetMultipleAccountsApi, Rpc } from '@solana/rpc';
import { type CompilableTransaction, type ITransactionWithSignatures } from '@solana/transactions';
type DecodeTransactionConfig = FetchAccountsConfig & {
    lastValidBlockHeight?: bigint;
};
export declare function decodeTransaction(encodedTransaction: Uint8Array, rpc: Rpc<GetMultipleAccountsApi>, config?: DecodeTransactionConfig): Promise<CompilableTransaction | (CompilableTransaction & ITransactionWithSignatures)>;
export {};
//# sourceMappingURL=decode-transaction.d.ts.map