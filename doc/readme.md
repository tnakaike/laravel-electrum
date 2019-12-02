# Laravel and Electrum APIs

| URL | Laravel frontend (src/App/Api/*.php) | Laravel backend (src/Electrum.php) | Electrum (electrum/commands.py)|
----|----|----|----
| POST /api/wallet/create | WalletController.create | createWallet | create_new (create a new wallet with key and seed), create_from_seed (create a wallet from a given seed) |
| POST /api/wallet/load | WalletController.load | loadWallet | load_wallet (to a daemon) |
| POST /api/wallet/multisig/create | WalletController.createMultisigWallet | createMultisigWallet | create_multisig_wallet (create a multisig wallet from a given seed) |
| POST /api/wallet/multisigkey/create | WalletController.createMultisigKey | createMultisigKey | create_new_multisig_key (create a new key), create_multisig_key (create a key from a given seed) |
| GET /api/status | StatusController.__invoke | getVersion, getBalance, isSynchronized | version (get an electrum version), getbalance (get the current balance), is_synchronized (check if a wallet is synchronized with a bitcoin network) |
| GET /api/addresses/unused | AddressController.unused | getUnusedAddress | getunusedaddress (get unused addresses) |
| GET /api/history | HistoryController.index | getHistory | history (get the transaction history) |
| POST /api/payment/getmax | PaymentController.getmax  | getmax | getmax (get the maximum amount of coins to be sent) |
| POST /api/payment | PaymentController.create | createPayment | payto (create a payment transaction) |
| POST /api/payment/broadcast | PaymentController.broadcast | broadcast | broadcast (broadcast a transaction) |
| POST /api/payment/sign | PaymentController.sign | sign | signtransaction (sign a transaction) |
| POST /api/requests | RequestsController.create | createRequest | addrequest (create a payment request) |
| DELETE /api/requests/ | RequestsController.clear | clearRequests | clearrequests (clear all of the payment requests) |
| GET /api/requests | RequestsController.index | getRequests | listrequests (get the list of payment requests) |
