# Laravel and Electrum APIs

| URL | Laravel frontend (src/App/Api/*.php) | Laravel backend (src/Electrum.php) | Electrum (electrum/commands.py) | Description |
----|----|----|----|----
| POST /api/wallet/create | WalletController.create | createWallet | create_new | Create a new wallet with key and seed |
|||| create_from_seed | Create a wallet from a given seed |
| POST /api/wallet/load | WalletController.load | loadWallet | load_wallet (to a daemon) | Load a wallet |
| POST /api/wallet/multisig/create | WalletController.createMultisigWallet | createMultisigWallet | create_multisig_wallet | Create a multisig wallet from a given seed |
| POST /api/wallet/multisigkey/create | WalletController.createMultisigKey | createMultisigKey | create_new_multisig_key | Create a new key |
|||| create_multisig_key | Create a key from a given seed |
| GET /api/status | StatusController.__invoke | getVersion | version | Get an electrum version |
||| getBalance | getbalance | Get the current balance |
||| isSynchronized | is_synchronized | Check if a wallet is synchronized with a bitcoin network |
| GET /api/addresses/unused | AddressController.unused | getUnusedAddress | getunusedaddress | Get unused addresses |
| GET /api/history | HistoryController.index | getHistory | history | Get a transaction history |
| POST /api/payment/getmax | PaymentController.getmax  | getmax | getmax | Get the maximum amount of coins to be sent |
| POST /api/payment | PaymentController.create | createPayment | payto | Create a payment transaction |
| POST /api/payment/broadcast | PaymentController.broadcast | broadcast | broadcast | Broadcast a transaction |
| POST /api/payment/sign | PaymentController.sign | sign | signtransaction | Sign a transaction |
| POST /api/requests | RequestsController.create | createRequest | addrequest | Create a payment request |
| DELETE /api/requests/ | RequestsController.clear | clearRequests | clearrequests | Clear all of the payment requests |
| GET /api/requests | RequestsController.index | getRequests | listrequests | Get the list of payment requests |
