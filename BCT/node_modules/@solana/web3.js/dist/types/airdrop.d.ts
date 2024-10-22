import type { Signature } from '@solana/keys';
import type { GetSignatureStatusesApi, RequestAirdropApi, Rpc } from '@solana/rpc';
import type { RpcSubscriptions, SignatureNotificationsApi } from '@solana/rpc-subscriptions';
import { requestAndConfirmAirdrop_INTERNAL_ONLY_DO_NOT_EXPORT } from './airdrop-internal.js';
type AirdropFunction = (config: Omit<Parameters<typeof requestAndConfirmAirdrop_INTERNAL_ONLY_DO_NOT_EXPORT>[0], 'confirmSignatureOnlyTransaction' | 'rpc'>) => Promise<Signature>;
type AirdropFactoryConfig = Readonly<{
    rpc: Rpc<GetSignatureStatusesApi & RequestAirdropApi>;
    rpcSubscriptions: RpcSubscriptions<SignatureNotificationsApi>;
}>;
export declare function airdropFactory({ rpc, rpcSubscriptions }: AirdropFactoryConfig): AirdropFunction;
export {};
//# sourceMappingURL=airdrop.d.ts.map