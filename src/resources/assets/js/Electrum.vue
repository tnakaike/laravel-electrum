<template>
    <div class="electrum">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="pull-right" v-if="is_loaded">
                    <div class="btn-group">
                        <a class="btn btn-sm btn-default btn-block dropdown-toggle" data-toggle="dropdown" href="#">
                            {{ pair }}
                            <small v-if="ticker[pair] !== undefined">({{ ticker[pair].symbol }})</small>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li v-for="currency in currencies">
                                <a @click="pair = currency;in_fiat = receive.amount * ticker[pair].last">
                                    {{ currency }}
                                    <small>({{ ticker[currency].symbol }})</small>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <ul class="nav nav-tabs">
                    <li :class="{active: active === '#history'}" v-if="is_loaded">
                        <a data-toggle="tab" href="#history" @click="setHash('history')">History</a>
                    </li>
                    <li :class="{active: active === '#requests'}" v-if="is_loaded">
                        <a data-toggle="tab" href="#requests" @click="setHash('requests')">Requests</a>
                    </li>
                    <li :class="{active: active === '#receive'}" v-if="is_loaded">
                        <a data-toggle="tab" href="#receive" @click="setHash('receive')">Receive</a>
                    </li>
                    <li :class="{active: active === '#send'}" v-if="is_loaded">
                        <a data-toggle="tab" href="#send" @click="setHash('send')">Send</a>
                    </li>
                    <li :class="{active: active === '#sign'}" v-if="is_loaded">
                        <a data-toggle="tab" href="#sign" @click="setHash('sign')">Sign</a>
                    </li>
                    <li :class="{active: active === '#wallet'}" v-if="is_loaded === false">
                        <a data-toggle="tab" href="#wallet" @click="setHash('wallet')">Wallet</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div id="history" class="tab-pane fade" :class="{'in active': active === '#history'}" v-if="is_loaded">
                        <div v-if="history.length">
                            <div class="table-header"></div>
                            <div class="table-responsive">
                                <table class="table table-condensed table-striped">
                                    <thead>
                                    <tr>
                                        <th class="no-wrap">
                                            <div>#</div>
                                        </th>
                                        <th class="no-wrap">
                                            <div>Date</div>
                                        </th>
                                        <th class="remaining">
                                            <div>Transaction</div>
                                        </th>
                                        <th class="no-wrap">
                                            <div>Amount</div>
                                        </th>
                                        <th class="no-wrap">
                                            <div>Balance</div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="transaction in history" @click="getTransactionDetails(transaction)"
                                        class="clickable">
                                        <td class="no-wrap">
                                    <span class="glyphicon"
                                          :class="{'glyphicon-ok text-success': transaction.confirmations >= 6}">
                                    </span>
                                        </td>
                                        <td class="no-wrap" v-text="transaction.date"></td>
                                        <td class="no-remaining" v-text="transaction.txid"></td>
                                        <td class="no-wrap" v-text="transaction.value"
                                            :class="{'text-danger': transaction.value < 0}"></td>
                                        <td class="no-wrap" v-text="transaction.total"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mt-8" v-else>
                            <strong class="mt-8">No history available</strong>
                        </div>
                    </div>

                    <div id="requests" class="tab-pane fade" :class="{'in active': active === '#requests'}" v-if="is_loaded">
                        <div v-if="requests.length">
                            <div class="table-header"></div>
                            <div class="table-responsive">
                                <table class="table table-condensed table-striped">
                                    <thead>
                                    <tr>
                                        <th class="no-wrap">
                                            <div>Status</div>
                                        </th>
                                        <th class="no-wrap">
                                            <div>Date</div>
                                        </th>
                                        <th class="no-wrap">
                                            <div>Address</div>
                                        </th>
                                        <th class="remaining">
                                            <div>Memo</div>
                                        </th>
                                        <th class="no-wrap">
                                            <div>Amount</div>
                                        </th>
                                        <th class="no-wrap">
                                            <div>&nbsp;</div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="request in requests" class="clickable" @click="getRequestDetails(request)">
                                        <td class="no-wrap" v-text="request.status"></td>
                                        <td class="no-wrap" v-text="request.date"></td>
                                        <td class="no-wrap" v-text="request.address"></td>
                                        <td class="remaining" v-text="request.memo"></td>
                                        <td class="no-wrap" v-text="request.amount_in_btc"></td>
                                        <td class="no-wrap">
                                            <button class="btn btn-xs btn-danger" @click.stop.prevent="removeRequest(request)">
                                                X
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mt-8" v-else>
                            <strong class="mt-8">No requests available</strong>
                        </div>
                    </div>

                    <div id="receive" class="tab-pane fade" :class="{'in active': active === '#receive'}" v-if="is_loaded">
                        <div class="row mt-8">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label>Receiving address</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" v-model="receive.address" class="form-control address"
                                               disabled>
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default copy"
                                                    :data-clipboard-text="receive.address">Copy</button>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Memo</label>
                                    <input type="text" v-model="receive.memo" class="form-control input-sm"
                                           placeholder="Enter a memo here">
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Amount in BTC</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" v-model="receive.amount"
                                                       class="form-control input-sm">
                                                <span class="input-group-addon">
                                                    BTC
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Amount in {{ pair }}</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" v-model="in_fiat"
                                                       class="form-control input-sm">
                                                <span class="input-group-addon">
                                                    {{ pair }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Expires</label>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-default btn-sm pull-right"
                                                    @click="addRequest"
                                                    :disabled="receive.amount === 0 || in_fiat === 0">
                                                <span class="glyphicon glyphicon-send"></span> Create request
                                            </button>
                                            <div class="btn-group">
                                                <a class="btn btn-sm btn-default btn-block dropdown-toggle"
                                                   data-toggle="dropdown" href="#">
                                                    {{ receive.expires ? receive.expires === 3600 ? '1 Hour' : receive.expires === 3600 * 24 ? '1 Day' : '1 Week' : 'Never'
                                                    }}
                                                    <span class="caret"></span>
                                                </a>
                                                <ul class="dropdown-menu">
                                                    <li><a @click="receive.expires = 0">Never</a></li>
                                                    <li><a @click="receive.expires = 3600">1 Hour</a></li>
                                                    <li><a @click="receive.expires = 3600 * 24">1 Day</a></li>
                                                    <li><a @click="receive.expires = 3600 * 24 * 7">1 Week</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="qr-code img-thumbnail img-responsive" style="margin-top:25px;">
                                    <qr-code
                                            :text="'bitcoin:' + receive.address + '?amount=' + receive.amount"></qr-code>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="send" class="tab-pane fade" :class="{'in active': active === '#send'}" v-if="is_loaded">
                        <div class="row mt-8">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label>Destination address</label>
                                    <div class="input-group input-group-sm col-xs-10">
                                        <input type="text" v-model="payment.destination" class="form-control input-lg">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Wallet password</label>
                                    <div class="input-group input-group-sm col-xs-10">
                                        <input type="password" v-model="payment.password" class="form-control input-lg">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Amount in BTC</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" v-model="payment.amount"
                                                       class="form-control input-sm">
                                                <span class="input-group-addon">
                                                    BTC
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Amount in {{ pair }}</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" v-model="pay_in_fiat"
                                                       class="form-control input-sm">
                                                <span class="input-group-addon">
                                                    {{ pair }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
				        <label style="color:white">Max</label>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-default btn-sm"
                                                    @click="getmax()"
                                                    :disabled="payment.destination === null || payment.password === null">
                                                <span class="glyphicon glyphicon-send"></span> Max
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
				        <label style="color:white">Send</label>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-default btn-sm"
                                                    @click="createPayment"
                                                    :disabled="payment.amount === 0 || pay_in_fiat === 0 || payment.destination === null || payment.password === null">
                                                <span class="glyphicon glyphicon-send"></span> Send
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Partial transaction</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" v-model="partial_tx_for_payment.hex" class="form-control address"
                                               disabled>
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default copy"
                                                    :data-clipboard-text="partial_tx_for_payment.hex">Copy</button>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Signed transaction</label>
                                    <div class="input-group input-group-sm col-xs-10">
                                        <input type="text" v-model="signed_tx.tx" class="form-control address"
                                               disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="sign" class="tab-pane fade" :class="{'in active': active === '#sign'}" v-if="is_loaded">
                        <div class="row mt-8">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label>Partially-signed transaction</label>
                                    <div class="input-group input-group-sm col-xs-10">
                                        <input type="text" v-model="partial_tx_for_signing.hex" class="form-control input-lg">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Wallet password</label>
                                    <div class="input-group input-group-sm col-xs-10">
                                        <input type="password" v-model="partial_tx_for_signing.password" class="form-control input-lg">
                                        <span class="input-group-btn">
					    <button type="button" class="btn btn-default btn-sm"
						    @click="sign"
						    :disabled="partial_tx_for_signing.hex === null || partial_tx_for_signing.password === null">Sign</button>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Signed transaction</label>
                                    <div class="input-group input-group-sm col-xs-10">
                                        <input type="text" v-model="signed_tx.tx" class="form-control address"
                                               disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="wallet" class="tab-pane fade" :class="{'in active': active === '#wallet'}" v-if="is_loaded === false">
                      <div class="row mt-8">
			<div class="col-md-9">
			  <div class="row">
			    <div class="col-md-8">
			      <div class="form-group">
				<label>Please create your wallet!</label>
				<div class="input-group input-group-sm col-xs-12">
				  <input type="text" v-model="wallet.msg" class="form-control input-lg" disabled>
				</div>
			      </div>
			    </div>
			    <div class="col-md-4">
			      <label>Multisig wallet</label>
			      <div class="form-group">
				<div class="input-group input-group-sm col-xs-4">
				  <input type="checkbox" v-model="wallet.is_multisig" class="form-control input-md">
				</div>
			      </div>
                            </div>
			  </div>
			  <div class="form-group">
                            <label>Wallet password (Please input if you want to encrypt your wallet)</label>
                            <div class="input-group input-group-sm col-xs-10">
                              <input type="password" v-model="wallet.password" class="form-control input-lg">
                            </div>
			  </div>
			  <div class="form-group">
                            <label>Seed (Please input if you already have a seed)</label>
                            <div class="input-group input-group-sm col-xs-10">
                              <input type="text" v-model="wallet.seed" class="form-control input-lg">
                            </div>
			  </div>
			  <div class="form-group" v-if="wallet.is_multisig">
                            <label>Your public key. Please share it with your co-signer.</label>
                            <div class="input-group input-group-sm col-xs-10">
                              <input type="text" v-model="wallet.key" class="form-control input-lg" disabled>
                              <span class="input-group-btn">
                                <button type="button" class="btn btn-default copy"
                                        :data-clipboard-text="wallet.key">Copy</button>
                              </span>
                            </div>
			  </div>
			  <div class="form-group" v-if="wallet.is_multisig">
                            <label>Your co-signer public key</label>
                            <div class="input-group input-group-sm col-xs-10">
                              <input type="text" v-model="wallet.cosignerkey" class="form-control input-lg">
                            </div>
			  </div>
			  <div class="form-group">
			    <button type="button" class="btn btn-default btn-sm"
				    v-if="wallet.is_multisig && wallet.seed === null"
				    @click="createMultisigKey"
				    :disabled="wallet.status !== 'none' || wallet.key !== null"
				    >Create seed and key</button>
			    <button type="button" class="btn btn-default btn-sm"
				    v-if="wallet.is_multisig && wallet.seed !== null"
				    @click="createMultisigKey"
				    :disabled="wallet.status !== 'none' || wallet.key !== null"
				    >Create key</button>
			    <button type="button" class="btn btn-default btn-sm"
				    @click="createWallet"
				    :disabled="wallet.status !== 'none' || (wallet.is_multisig && (wallet.seed === null || wallet.key === null || wallet.cosignerkey === null))"
				    >Create wallet</button>
			    <button type="button" class="btn btn-default btn-sm"
				    @click="loadWallet"
				    :disabled="wallet.status !== 'created'"
				    >Load wallet</button>
			  </div>
			</div>
                      </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer" v-if="is_loaded">
                <div class="status pull-right">
                    Electrum {{ version }} | Synchronized: <span v-text="is_sync ? 'yes' : 'no'" :class="{
                    'text-success': is_sync,
                    'text-danger': !is_sync
                }"></span>
                </div>
                <div class="balance">
                    Balance: <span v-text="Number(Number(balance.confirmed).toFixed(8))"></span> BTC
                    <span v-if="balance.unconfirmed"> (+ <span
                            v-text="Number(Number(balance.unconfirmed).toFixed(8))"></span> BTC unconfirmed)</span>
                    <span> ({{( balance.confirmed * ticker[pair].last).toFixed(2) }} {{ pair }})</span>
                    <span> 1 BTC~{{ ticker[pair].last }} {{ pair }}</span>
                </div>
            </div>
        </div>

        <bootstrap-modal ref="tx_details" :needHeader="false" :needFooter="false" size="medium" v-if="transaction.txid">
            <div slot="body">
                <table class="table table-details table-condensed table-striped">
                    <tbody>
                    <tr>
                        <th class="no-wrap">Transaction:&nbsp;</th>
                        <td class="remaining" colspan="2">{{ transaction.txid }}</td>
                    </tr>
                    <tr>
                        <th class="no-wrap">Status:&nbsp;</th>
                        <td class="remaining">{{ transaction.confirmations }} confirmations</td>
                        <td class="qr-cell" rowspan="6">
                            <div class="qr-code img-thumbnail img-responsive">
                                <qr-code :text="transaction.txid"></qr-code>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="no-wrap">Label:&nbsp;</th>
                        <td class="remaining">{{ transaction.label ? transaction.label : 'none' }}</td>
                    </tr>
                    <tr>
                        <th class="no-wrap">Block:&nbsp;</th>
                        <td class="remaining">{{ transaction.height }}</td>
                    </tr>
                    <tr>
                        <th class="no-wrap">Date:&nbsp;</th>
                        <td class="remaining">{{ transaction.date }}</td>
                    </tr>
                    <tr>
                        <th class="no-wrap">{{ transaction.value > 0 ? 'Received' : 'Sent' }}:&nbsp;</th>
                        <td class="remaining">
                            {{ transaction.value < 0 ? transaction.value * -1 : transaction.value }} BTC
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </bootstrap-modal>

        <bootstrap-modal ref="request_details" :needHeader="false" :needFooter="false" size="medium" v-if="request.id">
            <div slot="body">
                <table class="table table-details table-condensed table-striped">
                    <tbody>
                    <tr>
                        <th class="no-wrap">Address:&nbsp;</th>
                        <td class="remaining" colspan="2">{{ request.address }}</td>
                    </tr>
                    <tr>
                        <th class="no-wrap">Status:&nbsp;</th>
                        <td class="remaining">{{ request.status }}</td>
                        <td class="qr-cell" rowspan="6">
                            <div class="qr-code img-thumbnail img-responsive">
                                <qr-code :text="request.URI"></qr-code>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="no-wrap">Memo:&nbsp;</th>
                        <td class="remaining">{{ request.memo ? request.memo : 'none' }}</td>
                    </tr>
                    <tr>
                        <th class="no-wrap">Date:&nbsp;</th>
                        <td class="remaining">{{ request.date }}</td>
                    </tr>
                    <tr>
                        <th class="no-wrap">Expires:&nbsp;</th>
                        <td class="remaining">{{ request.exp ? expires : 'never' }}</td>
                    </tr>
                    <tr>
                        <th class="no-wrap">Amount:&nbsp;</th>
                        <td class="remaining">
                            {{ request.amount_in_btc }} BTC
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </bootstrap-modal>
    </div>
</template>

<script>
    import _ from 'lodash';
    import axios from 'axios';
    import moment from 'moment';
    import Clipboard from 'clipboard';
    import qrCode from 'vue-qrcode-component';
    import bootstrapModal from 'vue2-bootstrap-modal';

    export default {
        props: ['prefix', 'currency'],
        components: {
            qrCode, bootstrapModal
        },
        data() {
            return {
                /** Loaded statuses */
                address_is_loaded: false,
                history_is_loaded: false,
                requests_are_loaded: false,
                status_is_loaded: false,
                ticker_is_loaded: false,

                /** Application state */
                active: '#requests',
                clipboard: false,
                is_sync: false,
                version: null,
                timer: false,
                pair: null,
                in_fiat: 0,
                pay_in_fiat: 0,
                ticker: {},
                balance: {
                    confirmed: 0,
                    unconfirmed: 0
                },

                /** Raw unprocessed responses */
                raw: {
                    requests: [],
                    history: []
                },

                /** Receive model */
                receive: {
                    address: null,
                    memo: null,
                    amount: 0,
                    expires: 0
                },

                /** Request model */
                request: {
                    URI: null,
                    address: null,
                    amount: 0,
                    amount_in_btc: 0,
                    date: null,
                    exp: null,
                    id: null,
                    memo: null,
                    status: null,
                    time: 0
                },

                /** Transaction model */
                transaction: {
                    complete: false,
                    confirmations: 0,
                    date: null,
                    final: false,
                    height: 0,
                    hex: null,
                    input_addresses: [],
                    label: null,
                    output_addresses: [],
                    timestamp: 0,
                    txid: null,
                    value: 0
                },

                /** Payment model */
                payment: {
		    destination: null,
		    password: null,
		    amount: 0
                },

		/** Max amount model */
		max: {
		    max: 0
		},

                /** Partial transaction model for payment */
		partial_tx_for_payment: {
		    hex: null,
		    complete: false,
		    final: false
		},

                /** Partial transaction model for signing */
		partial_tx_for_signing: {
		    hex: null,
		    password: null
		},

                /** Partial transaction model for broadcast */
		partial_tx_for_broadcast: {
		    hex: null,
		    complete: false,
		    final: false
		},

                /** Signed transaction model*/
		signed_tx: {
		    complete: false,
		    tx: null
		},

    		/** Wallet model*/
		wallet: {
		    passowrd: null,
		    seed: null,
		    status: 'none',
		    msg: 'You do not have a wallet',
		    is_multisig: false,
		    key: null,
		    cosignerkey: null
		}
            }
        },
        watch: {
            /** Keep the BTC input formatted and update the fiat field on change */
            'receive.amount': function (val) {
                let cleaned = String(val).replace(/[^0-9.]/g, '');
                let parts = cleaned.split('.');

                if (parts.length > 1) {
                    cleaned = parts[0] + '.' + parts[1];
                }

                if (Number(cleaned) !== 0 && Number(cleaned) < 0.00000001) {
                    cleaned = Number(0.00000001).toFixed(8);
                }

                this.receive.amount = cleaned;
                this.in_fiat = Number(Number(cleaned * this.ticker[this.pair].last).toFixed(2));
            },
            /** Keep the fiat input formatted and update the BTC field on change */
            'in_fiat': function (val) {
                let cleaned = String(val).replace(/[^0-9.]/g, '');
                let parts = cleaned.split('.');

                if (parts.length > 1) {
                    cleaned = parts[0] + '.' + parts[1];
                }

                if (Number(cleaned) !== 0 && Number(cleaned) < 0.01) {
                    cleaned = 0.01;
                }

                this.in_fiat = cleaned;
                this.receive.amount = Number(Number(cleaned / this.ticker[this.pair].last).toFixed(8));
            },
            'payment.amount': function (val) {
                let cleaned = String(val).replace(/[^0-9.]/g, '');
                let parts = cleaned.split('.');

                if (parts.length > 1) {
                    cleaned = parts[0] + '.' + parts[1];
                }

                if (Number(cleaned) !== 0 && Number(cleaned) < 0.00000001) {
                    cleaned = Number(0.00000001).toFixed(8);
                }

                this.payment.amount = cleaned;
                this.pay_in_fiat = Number(Number(cleaned * this.ticker[this.pair].last).toFixed(2));
            },
            'pay_in_fiat': function (val) {
                let cleaned = String(val).replace(/[^0-9.]/g, '');
                let parts = cleaned.split('.');

                if (parts.length > 1) {
                    cleaned = parts[0] + '.' + parts[1];
                }

                if (Number(cleaned) !== 0 && Number(cleaned) < 0.01) {
                    cleaned = 0.01;
                }

                this.pay_in_fiat = cleaned;
                this.payment.amount = Number(Number(cleaned / this.ticker[this.pair].last).toFixed(8));
            },
        },
        computed: {
            /** Loaded status */
            is_loaded: function () {
                return this.address_is_loaded && this.history_is_loaded && this.requests_are_loaded && this.status_is_loaded;
            },

            /** Expiration for current request */
            expires: function () {
                return moment.unix(this.request.time + this.request.exp).fromNow();
            },

            /** Available currencies in the ticker */
            currencies: function () {
                return Object.keys(this.ticker);
            },

            /** Processed history */
            history: function () {
                let vm = this,
                    history = [],
                    total = 0;

                _.each(vm.raw.history, function (o) {
                    total += o.value;
                    o.total = Number(total.toFixed(8));
                    history.push(o);
                });

                return history.reverse();
            },

            /** Processed request */
            requests: function () {
                let vm = this,
                    requests = [];

                _.each(vm.raw.requests, function (o) {
                    o.amount_in_btc = Number(Number(o.amount / 100000000).toFixed(8));
                    o.date = moment.unix(o.time).format('YYYY-MM-DD HH:mm');
                    requests.push(o);
                });

                return requests;
            }
        },
        /**
         * Set active tab on creation
         */
        created() {
            if (!window.location.hash) {
                window.location.hash = this.active;
            } else {
                this.active = window.location.hash;
            }
        },
        /**
         * Initialize clipboard and load data
         */
        mounted() {
            if (Clipboard.isSupported()) {
                this.clipboard = new Clipboard('.btn.copy');
            }

            this.pair = this.currency;

            this.getAddress();
            this.getHistory();
            this.getRequests();
            this.getStatus();

            this.timer = setInterval(() => {
                this.getStatus();
            }, 60000);
        },
        methods: {
            /**
             * Add a new payment request
             */
            addRequest() {
                let vm = this;

                axios.post('/' + vm.prefix + '/api/requests', {
                    memo: vm.receive.memo,
                    amount: vm.receive.amount,
                    expires: vm.receive.expires
                }).then((response) => {
                    vm.raw.requests.unshift(response.data);
                    vm.setHash('requests');
                    vm.getAddress();
                }).catch((error) => {
                    console.error(error);
                });
            },

            /**
             * Get an unused address
             */
            getAddress() {
                let vm = this;

                vm.address_is_loaded = false;
                axios.get('/' + vm.prefix + '/api/addresses/unused').then((response) => {
                    Object.assign(vm.receive, {
                        address: response.data,
                        memo: null,
                        amount: 0,
                        expires: 0
                    });
                    vm.address_is_loaded = true;
                }).catch((error) => {
                    console.error(error);
                });
            },

            /**
             * Get wallet history
             */
            getHistory() {
                let vm = this;

                vm.history_is_loaded = false;
                axios.get('/' + vm.prefix + '/api/history').then((response) => {
                    Object.assign(vm.raw.history, response.data);
                    vm.history_is_loaded = true;
                }).catch((error) => {
                    console.error(error);
                });
            },

            /**
             * Get request details
             *
             * @param request
             */
            getRequestDetails(request) {
                let vm = this;

                vm.request = request;
                vm.$nextTick(function () {
                    vm.$refs.request_details.open();
                });
            },

            /**
             * Get all requests
             */
            getRequests() {
                let vm = this;

                vm.requests_are_loaded = false;
                axios.get('/' + vm.prefix + '/api/requests').then((response) => {
                    Object.assign(vm.raw.requests, response.data);
                    vm.requests_are_loaded = true;
                }).catch((error) => {
                    console.error(error);
                });
            },

            /**
             * Get wallet status
             */
            getStatus() {
                let vm = this;

                axios.get('/' + vm.prefix + '/api/status').then((response) => {
                    Object.assign(vm, response.data);
                    vm.status_is_loaded = true;
                }).catch((error) => {
                    console.error(error);
                });
            },

            /**
             * Get transaction details
             *
             * @param transaction
             */
            getTransactionDetails(transaction) {
                let vm = this;

                vm.transaction = transaction;
                vm.$nextTick(function () {
                    vm.$refs.tx_details.open();
                });
            },

            /**
             * Remove request
             *
             * @param request
             */
            removeRequest(request) {
                let vm = this;

                axios.delete('/' + vm.prefix + '/api/requests/' + request.address).then((response) => {
                    if (response.data) {
                        vm.raw.requests = _.filter(vm.raw.requests, function (o) {
                            return o.id !== request.id;
                        });
                    }
                }).catch((error) => {
                    console.error(error);
                });
            },

	    /**
             * Get the maximum amount that can be sent
             */
            getmax() {
                let vm = this;

                axios.post('/' + vm.prefix + '/api/payment/getmax', {
                    destination: vm.payment.destination,
		    password: vm.payment.password
                }).then((response) => {
		    Object.assign(vm.max, response.data);
		    vm.payment.amount = vm.max.max;
                }).catch((error) => {
                    console.error(error);
                });
            },

	    /**
             * Create a payment
             */
            createPayment() {
                let vm = this,
		    tmp = [];

                axios.post('/' + vm.prefix + '/api/payment', {
                    amount: vm.payment.amount,
                    destination: vm.payment.destination,
		    password: vm.payment.password
                }).then((response) => {
		    Object.assign(vm.partial_tx_for_payment, response.data);
		    if (vm.partial_tx_for_payment.complete == true) {
			axios.post('/' + vm.prefix + '/api/payment/broadcast', {
			    tx: vm.partial_tx_for_payment.hex
			}).then((response) => {
			    Object.assign(tmp, response.data);
			    vm.signed_tx.complete = tmp[0];
			    vm.signed_tx.tx = tmp[1];
			}).catch((error) => {
			    console.error(error);
			});
		    } else {
			vm.signed_tx.complete = false;
			vm.signed_tx.tx = 'Waiting for one or more co-signers to sign';
		    }
                }).catch((error) => {
                    console.error(error);
                });
            },

	    /**
             * Sign a transaction
             */
            sign() {
                let vm = this,
		    tmp = [];

                axios.post('/' + vm.prefix + '/api/payment/sign', {
                    tx: vm.partial_tx_for_signing.hex,
                    password: vm.partial_tx_for_signing.password
                }).then((response) => {
		    Object.assign(vm.partial_tx_for_broadcast, response.data);

		    axios.post('/' + vm.prefix + '/api/payment/broadcast', {
			tx: vm.partial_tx_for_broadcast.hex
                    }).then((response) => {
			Object.assign(tmp, response.data);
			vm.signed_tx.complete = tmp[0];
			vm.signed_tx.tx = tmp[1];
                    }).catch((error) => {
			console.error(error);
                    });
                }).catch((error) => {
                    console.error(error);
                });

            },

	    /**
             * Create a wallet
             */
            createWallet() {
                let vm = this;

		vm.wallet.msg = 'Creating your wallet';

		if (vm.wallet.is_multisig) {
                    axios.post('/' + vm.prefix + '/api/wallet/multisig/create', {
			seed: vm.wallet.seed,
			cosignerkey: vm.wallet.cosignerkey,
			password: vm.wallet.password
                    }).then((response) => {
			Object.assign(vm.wallet, response.data);
			vm.wallet.msg = 'Your wallet created! Please load your wallet after recording your seed.';
			vm.wallet.status = 'created';
                    }).catch((error) => {
			console.error(error);
                    });
		} else {
                    axios.post('/' + vm.prefix + '/api/wallet/create', {
			seed: vm.wallet.seed,
			password: vm.wallet.password
                    }).then((response) => {
			Object.assign(vm.wallet, response.data);
			vm.wallet.msg = 'Your wallet created! Please load your wallet after recording your seed.';
			vm.wallet.status = 'created';
                    }).catch((error) => {
			console.error(error);
                    });
		}

            },

	    /**
             * Load a wallet
             */
            loadWallet() {
                let vm = this;

		vm.wallet.msg = 'Loading your wallet';
		
		axios.post('/' + vm.prefix + '/api/wallet/load', {
		    password: vm.wallet.password
                }).then((response) => {
		    Object.assign(vm.wallet, response.data);
		    vm.wallet.msg = 'Your wallet loaded! Please reload to look at your wallet.';
		    vm.wallet.status = 'loaded';
                }).catch((error) => {
		    console.error(error);
                });
            },

	    /**
             * Create a key for a multisig wallet
             */
            createMultisigKey() {
                let vm = this;

		vm.wallet.msg = 'Creating your key';

		axios.post('/' + vm.prefix + '/api/wallet/multisigkey/create', {
		    seed: vm.wallet.seed
                }).then((response) => {
		    Object.assign(vm.wallet, response.data);
		    console.log(wallet.seed);
		    console.log(wallet.key);
		    vm.wallet.msg = 'Your key created! Please input the public key of your co-signer to create a multisig wallet.';
                }).catch((error) => {
		    console.error(error);
                });

	    },

            /**
             * Set the active tab/hash
             *
             * @param hash
             */
            setHash(hash) {
                window.location.hash = "#" + hash;
                this.active = window.location.hash;
            }
        }
    }
</script>

<style lang="scss">
    .electrum {
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 13px;
        a {
            cursor: pointer;
        }
        .mt-8 {
            margin-top: 8px;
        }
        .tab-pane {
            position: relative;
        }
        .table-header {
            height: 35px;
            border-bottom: 2px solid #ddd;
        }
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        td, .address {
            font-family: "Inconsolata", "Fira Mono", "Source Code Pro", Monaco, Consolas, "Lucida Console", monospace;
        }
        .table {
            margin-bottom: 0;
            border-top: none;
            &.table-details {
                tbody, th, td {
                    border: 1px solid #ddd;
                }
                th, td {
                    padding-left:5px;
                }
            }
            th, tr:first-of-type, tr:first-of-type td {
                border-top: none;
            }
            th {
                height: 0;
                margin: 0;
                padding: 0;
                border-bottom: none;
                div {
                    position: absolute;
                    padding: 9px 20px 9px 30px;
                    top: 0;
                    margin-left: -25px;
                    line-height: normal;
                }
            }
            .btn {
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                font-size: 10px;
                font-weight: bold;
            }
            .no-wrap {
                white-space: nowrap;
                vertical-align: middle;
            }
            .remaining {
                width: 99%;
                max-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                vertical-align: middle;
            }
        }
        .clickable {
            cursor: pointer;
        }
        .form-horizontal .control-label.text-left {
            text-align: left;
        }
        .qr-cell {
            min-width:160px;
            padding:5px 5px 0 5px;
        }
        .qr-code {
            canvas {
                visibility: hidden;
            }
            img {
                width: 100%;
            }
        }
    }
</style>
