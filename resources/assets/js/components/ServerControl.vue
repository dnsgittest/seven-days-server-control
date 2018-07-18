<template>
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-4"></div>
			<div class="col-sm-4 text-center">
				<h5>Server Ip:</h5>
			</div>
			<div class="col-sm-4"></div>
		</div>
		<div class="row">
			<div class="col-sm-4"></div>
			<div class="col-sm-4 text-center">
				<h4>{{serverIp}}</h4>
			</div>
			<div class="col-sm-4"></div>
		</div>
		<div class="row">
			<hr />
		</div>
		<div class="row text-center">
			<div class="col-sm-4"></div>
			<div class="col-sm-4 text-center">
				<button class="btn"
					:class="buttonColor"
					@click="toggleServerState"
				>{{buttonText}}</button>
			</div>
			<div class="col-sm-4"></div>
		</div>
	</div>
</template>

<script type="script/babel">
	
	import HttpClient from 'axios';

	export default {
		mounted () {
			this.listen();
			this.getSererState()
		},
		data () {
			return {
				re_calculate: 0,
				state: ''
			};
		},
		asyncComputed: {
			serverIp: {
				get () {
					return HttpClient.get('get-server-ip')
						.then(response => response.data.ip);
				},
				watch () {
					this.re_calculate;
				}
			}
		},
		computed: {
			buttonText () {
				let text = 'Waiting...';
				if (this.state === 'running') {
					text = 'Stop Server';
				} else if (this.state === 'stopped') {
					text = 'Start Server';
				} else if (this.state === 'starting') {
					text = 'Server is starting.'
				} else if (this.state === 'stopping') {
					text = 'Server is stopping.'
				}
				return text;
			},
			buttonColor () {
				let color = 'btn-warning';
				if (this.state === 'stopped') {
					color = 'btn-success'
				} else if (this.state === 'starting' || this.serverState === 'stopping') {
					color = 'btn-warning';
				} else if (this.state === 'running') {
					color = 'btn-danger';
				}
				return color;
			}
		},
		methods: {
			toggleServerState () {
				if (this.state === 'running') {
					HttpClient.get(
						'shutdown-server'
					).then((res) => {
						this.re_calculate++;
					});
				} else if (this.state === 'stopped') {
					HttpClient.get(
						'start-server'
					).then((res) => {
						this.re_calculate++;
					});
				}
			},
			listen () {
				Echo.channel('server-info')
					.listen('ServerStateChanged', e => {
						this.state = e.state;
					});
			},
			getSererState () {
				HttpClient.get('get-server-state')
					.then(response => this.state = response.data.state);
			}
		}
	};

</script>