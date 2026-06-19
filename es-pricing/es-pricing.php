<?php
/**
 * Plugin Name:       ES Pricing Tables
 * Plugin URI:        https://www.theemptyspace.com
 * Description:       Interactive pricing table with monthly/annual toggle and discount selector. Add to any page with [es_pricing]. Configure plans and content in Settings → ES Pricing.
 * Version:           1.1.0
 * Requires at least: 5.8
 * Tested up to:      6.7
 * Author:            EmptySpace Technology
 * License:           GPL-2.0+
 */

defined( 'ABSPATH' ) || exit;

define( 'ESP_VERSION', '1.1.0' );
define( 'ESP_DIR',     plugin_dir_path( __FILE__ ) );
define( 'ESP_URL',     plugin_dir_url( __FILE__ ) );
define( 'ESP_OPTION',  'es_pricing_v1' );

/* ─── Default data ─────────────────────────────────────────────────────────── */

function esp_defaults() {
	return [
		'plans' => [
			[
				'name'             => 'Free',
				'tagline'          => "See What's Possible",
				'is_free'          => '1',
				'is_enterprise'    => '',
				'monthly'          => '',
				'annual_per_month' => '',
				'annual_total'     => '',
				'limit'            => 'Up to 100 items',
				'features'         => "Unlimited admins & managers\nCategory-based permissions\nUnlimited file storage\nKnowledgebase & email support",
			],
			[
				'name'             => 'Starter',
				'tagline'          => 'Getting Serious',
				'is_free'          => '',
				'is_enterprise'    => '',
				'monthly'          => '25',
				'annual_per_month' => '22.50',
				'annual_total'     => '270',
				'limit'            => 'Up to 500 items',
				'features'         => "Unlimited admins & managers — no per-seat fees\nCategory-based permissions, not just site-wide\nUnlimited file storage\nCategory setup consultation",
			],
			[
				'name'             => 'Professional',
				'tagline'          => 'Going for It',
				'is_free'          => '',
				'is_enterprise'    => '',
				'monthly'          => '45',
				'annual_per_month' => '40.50',
				'annual_total'     => '486',
				'limit'            => 'Up to 2,000 items',
				'features'         => "Unlimited admins & managers — no per-seat fees\nCategory-based permissions, not just site-wide\nUnlimited file storage\nCategory setup + workflow consultation\nImport existing inventory",
			],
			[
				'name'             => 'Professional Plus',
				'tagline'          => 'Scaling Up',
				'is_free'          => '',
				'is_enterprise'    => '',
				'monthly'          => '75',
				'annual_per_month' => '67.50',
				'annual_total'     => '810',
				'limit'            => 'Up to 5,000 items',
				'features'         => "Unlimited admins & managers — no per-seat fees\nCategory-based permissions, not just site-wide\nUnlimited file storage\nSetup, workflow & storage optimization\nImport existing inventory",
			],
			[
				'name'             => 'Organization',
				'tagline'          => 'Not Slowing Down',
				'is_free'          => '',
				'is_enterprise'    => '',
				'monthly'          => '95',
				'annual_per_month' => '85.50',
				'annual_total'     => '1026',
				'limit'            => 'Up to 7,500 items',
				'features'         => "Unlimited admins & managers — no per-seat fees\nCategory-based permissions, not just site-wide\nUnlimited file storage\nFull consultation across departments\nImport existing inventory",
			],
			[
				'name'             => 'Organization Plus',
				'tagline'          => 'Full Scale',
				'is_free'          => '',
				'is_enterprise'    => '',
				'monthly'          => '125',
				'annual_per_month' => '112.50',
				'annual_total'     => '1350',
				'limit'            => 'Up to 10,000 items',
				'features'         => "Unlimited admins & managers — no per-seat fees\nCategory-based permissions, not just site-wide\nUnlimited file storage\nFull consultation across departments\nImport existing inventory",
			],
			[
				'name'             => 'Enterprise',
				'tagline'          => 'No Limits, No Compromises',
				'is_free'          => '',
				'is_enterprise'    => '1',
				'monthly'          => '',
				'annual_per_month' => '',
				'annual_total'     => '',
				'limit'            => 'Unlimited items, sized to you',
				'features'         => "Unlimited admins & managers\nCategory-based permissions\nUnlimited file storage\nFully custom consultation\nTailored to your operation",
			],
		],
		'discounts' => [
			[ 'label' => 'Individual / General',                              'value' => '0',  'note' => '' ],
			[ 'label' => 'Currently Enrolled Student',                        'value' => '50', 'note' => '50% discount — eligibility verified at signup' ],
			[ 'label' => 'Secondary Ed Theatre Teacher',                      'value' => '20', 'note' => '20% discount — eligibility verified at signup' ],
			[ 'label' => 'K-12 School or Institution',                        'value' => '20', 'note' => '20% discount — eligibility verified at signup' ],
			[ 'label' => 'College or University',                             'value' => '15', 'note' => '15% discount — eligibility verified at signup' ],
			[ 'label' => 'Nonprofit Organization (501c3)',                    'value' => '10', 'note' => '10% discount — eligibility verified at signup' ],
			[ 'label' => 'Theatre Professional (AEA, USITT, IATSE, etc.)',   'value' => '10', 'note' => '10% discount — eligibility verified at signup' ],
		],
		'cta_url'              => 'https://www.theemptyspace.com/signup/',
		'cta_text'             => 'Sign Up Now',
		'cta_note'             => 'Free plan available — no credit card required. Paid plans include a 30-day free trial.',
		'annual_savings_label' => 'Save 10%',
		'modal_library'        => 'magnific',
		'accent_color'         => '#FE5000',
	];
}

function esp_hex_to_rgb( $hex ) {
	$hex = ltrim( $hex, '#' );
	if ( strlen( $hex ) === 3 ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	return [
		hexdec( substr( $hex, 0, 2 ) ),
		hexdec( substr( $hex, 2, 2 ) ),
		hexdec( substr( $hex, 4, 2 ) ),
	];
}

function esp_get_settings() {
	$saved = get_option( ESP_OPTION, null );
	if ( $saved === null ) {
		return esp_defaults();
	}
	$defaults = esp_defaults();
	foreach ( [ 'cta_url', 'cta_text', 'cta_note', 'annual_savings_label', 'modal_library', 'accent_color' ] as $key ) {
		if ( empty( $saved[ $key ] ) ) {
			$saved[ $key ] = $defaults[ $key ];
		}
	}
	if ( empty( $saved['plans'] ) )     $saved['plans']     = $defaults['plans'];
	if ( empty( $saved['discounts'] ) ) $saved['discounts'] = $defaults['discounts'];
	return $saved;
}

/* ─── Plugin action links (Settings link on Plugins page) ──────────────────── */

add_filter( 'plugin_action_links_es-pricing/es-pricing.php', function ( $links ) {
	$settings_link = '<a href="' . admin_url( 'options-general.php?page=es-pricing' ) . '">' . __( 'Settings' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
} );

/* ─── Shortcode ────────────────────────────────────────────────────────────── */

add_shortcode( 'es_pricing', 'esp_shortcode' );

function esp_shortcode( $atts ) {
	$s = esp_get_settings();

	wp_enqueue_style(
		'es-pricing',
		ESP_URL . 'assets/pricing.css',
		[],
		ESP_VERSION
	);

	wp_enqueue_script(
		'es-pricing',
		ESP_URL . 'assets/pricing.js',
		[ 'jquery' ],
		ESP_VERSION,
		true
	);

	$plans_js = [];
	foreach ( $s['plans'] as $plan ) {
		$raw_features = isset( $plan['features'] ) ? $plan['features'] : '';
		$features     = array_values(
			array_filter( array_map( 'trim', explode( "\n", $raw_features ) ) )
		);
		$plans_js[] = [
			'name'           => $plan['name'],
			'tagline'        => $plan['tagline'],
			'isFree'         => ! empty( $plan['is_free'] ),
			'isEnterprise'   => ! empty( $plan['is_enterprise'] ),
			'monthly'        => floatval( $plan['monthly'] ),
			'annualPerMonth' => floatval( $plan['annual_per_month'] ),
			'annualTotal'    => floatval( $plan['annual_total'] ),
			'limit'          => $plan['limit'],
			'features'       => $features,
		];
	}

	wp_localize_script( 'es-pricing', 'esPricingData', [
		'plans'        => $plans_js,
		'modalLibrary' => in_array( $s['modal_library'], [ 'magnific', 'fancybox' ], true )
		                    ? $s['modal_library'] : 'magnific',
	] );

	$cta_url  = esc_url( $s['cta_url'] );
	$cta_text = esc_html( $s['cta_text'] );
	$cta_note = esc_html( $s['cta_note'] );
	$save_lbl = esc_html( $s['annual_savings_label'] );

	$accent     = sanitize_hex_color( $s['accent_color'] ) ?: '#FE5000';
	$accent_rgb = esp_hex_to_rgb( $accent );

	ob_start();
	?>
	<style>#es-pricing{--esp-accent:<?php echo esc_attr( $accent ); ?>;--esp-accent-rgb:<?php echo esc_attr( implode( ',', $accent_rgb ) ); ?>;}</style>
	<div class="es-pricing" id="es-pricing">

		<div class="es-controls">
			<div class="es-toggle">
				<button class="es-toggle-btn active" data-period="monthly">Monthly</button>
				<button class="es-toggle-btn" data-period="annual">
					Annual <span class="es-save-chip"><?php echo $save_lbl; ?></span>
				</button>
			</div>
			<div class="es-who">
				<label for="es-who-select">I'm a</label>
				<select class="es-who-select" id="es-who-select">
					<?php foreach ( $s['discounts'] as $d ) :
						$val = floatval( $d['value'] ) / 100;
					?>
					<option value="<?php echo esc_attr( $val ); ?>"
					        data-note="<?php echo esc_attr( $d['note'] ); ?>">
						<?php echo esc_html( $d['label'] ); ?>
					</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="es-discount-note" id="es-discount-note"></div>
		<div class="es-cards" id="es-cards"></div>

		<div class="es-cta-wrap">
			<a href="<?php echo $cta_url; ?>"
			   class="es-cta-btn fancybox-signup"
			   target="_blank"
			   rel="noopener">
				<?php echo $cta_text; ?>
			</a>
			<p class="es-cta-note"><?php echo $cta_note; ?></p>
		</div>

		<p class="es-discount-asterisk" id="es-discount-asterisk" style="display:none">
			* Discounts are applied at the individual or organizational account level and verified at signup.
			Discount percentages shown reflect the coupon applied to your base plan price.
		</p>

	</div>
	<?php
	return ob_get_clean();
}

/* ─── Admin ────────────────────────────────────────────────────────────────── */

add_action( 'admin_menu', function () {
	add_options_page(
		'ES Pricing',
		'ES Pricing',
		'manage_options',
		'es-pricing',
		'esp_admin_page'
	);
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( $hook !== 'settings_page_es-pricing' ) return;
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_style( 'esp-admin', ESP_URL . 'assets/admin.css', [ 'wp-color-picker' ], ESP_VERSION );
	wp_enqueue_script( 'wp-color-picker' );
	wp_add_inline_script( 'wp-color-picker',
		'jQuery(function($){ $(".esp-color-picker").wpColorPicker(); });'
	);
} );

function esp_sanitize_settings( $raw ) {
	$out      = [];
	$defaults = esp_defaults();

	$out['plans'] = [];
	foreach ( $defaults['plans'] as $i => $def ) {
		$p = isset( $raw['plans'][ $i ] ) ? $raw['plans'][ $i ] : [];
		$out['plans'][] = [
			'name'             => sanitize_text_field( $p['name']             ?? $def['name'] ),
			'tagline'          => sanitize_text_field( $p['tagline']          ?? $def['tagline'] ),
			'is_free'          => ( isset( $p['is_free'] ) && $p['is_free'] === '1' ) ? '1' : '',
			'is_enterprise'    => ( isset( $p['is_enterprise'] ) && $p['is_enterprise'] === '1' ) ? '1' : '',
			'monthly'          => sanitize_text_field( $p['monthly']          ?? $def['monthly'] ),
			'annual_per_month' => sanitize_text_field( $p['annual_per_month'] ?? $def['annual_per_month'] ),
			'annual_total'     => sanitize_text_field( $p['annual_total']     ?? $def['annual_total'] ),
			'limit'            => sanitize_text_field( $p['limit']            ?? $def['limit'] ),
			'features'         => sanitize_textarea_field( $p['features']     ?? $def['features'] ),
		];
	}

	$out['discounts'] = [];
	foreach ( $defaults['discounts'] as $i => $def ) {
		$d = isset( $raw['discounts'][ $i ] ) ? $raw['discounts'][ $i ] : [];
		$out['discounts'][] = [
			'label' => sanitize_text_field( $d['label'] ?? $def['label'] ),
			'value' => sanitize_text_field( $d['value'] ?? $def['value'] ),
			'note'  => sanitize_text_field( $d['note']  ?? $def['note'] ),
		];
	}

	$out['cta_url']              = esc_url_raw( $raw['cta_url']              ?? $defaults['cta_url'] );
	$out['cta_text']             = sanitize_text_field( $raw['cta_text']             ?? $defaults['cta_text'] );
	$out['cta_note']             = sanitize_text_field( $raw['cta_note']             ?? $defaults['cta_note'] );
	$out['annual_savings_label'] = sanitize_text_field( $raw['annual_savings_label'] ?? $defaults['annual_savings_label'] );
	$out['modal_library']        = in_array( $raw['modal_library'] ?? '', [ 'magnific', 'fancybox' ], true )
	                                 ? $raw['modal_library'] : 'magnific';
	$out['accent_color']         = sanitize_hex_color( $raw['accent_color'] ?? '' ) ?: '#FE5000';

	return $out;
}

function esp_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) return;

	$saved = false;
	if (
		isset( $_POST['esp_nonce'] ) &&
		wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['esp_nonce'] ) ), 'esp_save_settings' )
	) {
		$raw  = isset( $_POST['esp'] ) ? wp_unslash( $_POST['esp'] ) : []; // phpcs:ignore
		$data = esp_sanitize_settings( $raw );
		update_option( ESP_OPTION, $data );
		$saved = true;
	}

	$s = esp_get_settings();
	?>
	<div class="wrap esp-admin">
		<h1>ES Pricing Settings</h1>

		<?php if ( $saved ) : ?>
		<div class="notice notice-success is-dismissible"><p><strong>Settings saved.</strong></p></div>
		<?php endif; ?>

		<div class="essp-shortcode-hint">
			Add the pricing table to any page with the shortcode: <code>[es_pricing]</code>
		</div>

		<form method="post" action="">
			<?php wp_nonce_field( 'esp_save_settings', 'esp_nonce' ); ?>

			<!-- ── PLANS ── -->
			<h2 class="essp-section-title">Plans</h2>
			<p class="description" style="margin-bottom:12px">
				Edit each plan's details. <strong>Features:</strong> one per line.
				The item limit is automatically added as the first feature in the card.
			</p>

			<?php foreach ( $s['plans'] as $i => $plan ) :
				$is_free       = ! empty( $plan['is_free'] );
				$is_enterprise = ! empty( $plan['is_enterprise'] );
				$label         = ! empty( $plan['name'] ) ? $plan['name'] : "Plan $i";
			?>
			<details class="essp-plan-box">
				<summary class="essp-plan-summary">
					<span class="essp-plan-label"><?php echo esc_html( $label ); ?></span>
					<?php if ( $is_free ) : ?>
						<span class="essp-badge essp-badge-free">Free</span>
					<?php endif; ?>
					<?php if ( $is_enterprise ) : ?>
						<span class="essp-badge essp-badge-dark">Enterprise</span>
					<?php endif; ?>
				</summary>

				<div class="essp-plan-body">
					<input type="hidden" name="esp[plans][<?php echo $i; ?>][is_free]"
					       value="<?php echo $is_free ? '1' : ''; ?>">
					<input type="hidden" name="esp[plans][<?php echo $i; ?>][is_enterprise]"
					       value="<?php echo $is_enterprise ? '1' : ''; ?>">

					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label>Plan Name</label></th>
							<td>
								<input type="text" class="regular-text"
								       name="esp[plans][<?php echo $i; ?>][name]"
								       value="<?php echo esc_attr( $plan['name'] ); ?>">
							</td>
						</tr>
						<tr>
							<th scope="row"><label>Tagline</label></th>
							<td>
								<input type="text" class="regular-text"
								       name="esp[plans][<?php echo $i; ?>][tagline]"
								       value="<?php echo esc_attr( $plan['tagline'] ); ?>">
								<p class="description">Small label above the plan name in the card header.</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label>Item Limit</label></th>
							<td>
								<input type="text" class="regular-text"
								       name="esp[plans][<?php echo $i; ?>][limit]"
								       value="<?php echo esc_attr( $plan['limit'] ); ?>">
								<p class="description">e.g. "Up to 500 items" or "Up to 3 productions" — shown as the first feature.</p>
							</td>
						</tr>

						<?php if ( ! $is_free && ! $is_enterprise ) : ?>
						<tr>
							<th scope="row"><label>Monthly Price ($)</label></th>
							<td>
								<input type="number" step="0.01" min="0" style="width:90px"
								       name="esp[plans][<?php echo $i; ?>][monthly]"
								       value="<?php echo esc_attr( $plan['monthly'] ); ?>">
							</td>
						</tr>
						<tr>
							<th scope="row"><label>Annual Price / Month ($)</label></th>
							<td>
								<input type="number" step="0.01" min="0" style="width:90px"
								       name="esp[plans][<?php echo $i; ?>][annual_per_month]"
								       value="<?php echo esc_attr( $plan['annual_per_month'] ); ?>">
								<p class="description">Per-month equivalent when billed annually.</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label>Annual Total ($)</label></th>
							<td>
								<input type="number" step="0.01" min="0" style="width:90px"
								       name="esp[plans][<?php echo $i; ?>][annual_total]"
								       value="<?php echo esc_attr( $plan['annual_total'] ); ?>">
								<p class="description">Full amount charged per year.</p>
							</td>
						</tr>
						<?php else : ?>
						<tr>
							<td colspan="2" style="padding-left:0">
								<input type="hidden" name="esp[plans][<?php echo $i; ?>][monthly]"          value="">
								<input type="hidden" name="esp[plans][<?php echo $i; ?>][annual_per_month]" value="">
								<input type="hidden" name="esp[plans][<?php echo $i; ?>][annual_total]"     value="">
								<em style="color:#888;font-size:13px">
									<?php echo $is_free ? 'Price is always $0 — no fields needed.' : 'Enterprise pricing is handled via Contact Us / Stripe Quotes.'; ?>
								</em>
							</td>
						</tr>
						<?php endif; ?>

						<tr>
							<th scope="row"><label>Features</label></th>
							<td>
								<textarea name="esp[plans][<?php echo $i; ?>][features]"
								          rows="6" class="large-text"><?php echo esc_textarea( $plan['features'] ); ?></textarea>
								<p class="description">One feature per line.</p>
							</td>
						</tr>
					</table>
				</div>
			</details>
			<?php endforeach; ?>

			<!-- ── DISCOUNTS ── -->
			<h2 class="essp-section-title">Discount Options ("I'm a…" dropdown)</h2>
			<p class="description" style="margin-bottom:12px">
				These control the dropdown on the pricing page. Discount % is applied to displayed prices only —
				the actual coupon is applied via Stripe at signup.
			</p>

			<table class="widefat essp-discount-table">
				<thead>
					<tr>
						<th>Label (shown in dropdown)</th>
						<th style="width:100px">Discount&nbsp;%</th>
						<th>Note shown when selected</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $s['discounts'] as $i => $d ) : ?>
					<tr>
						<td>
							<input type="text" class="regular-text"
							       name="esp[discounts][<?php echo $i; ?>][label]"
							       value="<?php echo esc_attr( $d['label'] ); ?>">
						</td>
						<td>
							<input type="number" min="0" max="100" step="1" style="width:64px"
							       name="esp[discounts][<?php echo $i; ?>][value]"
							       value="<?php echo esc_attr( $d['value'] ); ?>">
						</td>
						<td>
							<input type="text" class="large-text"
							       name="esp[discounts][<?php echo $i; ?>][note]"
							       value="<?php echo esc_attr( $d['note'] ); ?>">
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<!-- ── CTA & SETTINGS ── -->
			<h2 class="essp-section-title">CTA &amp; General Settings</h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="esp-cta-url">Sign-up URL</label></th>
					<td>
						<input type="url" class="regular-text" id="esp-cta-url"
						       name="esp[cta_url]"
						       value="<?php echo esc_attr( $s['cta_url'] ); ?>">
						<p class="description">Opens in a Magnific Popup iframe (or FancyBox if Magnific is unavailable).</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="esp-cta-text">Button Text</label></th>
					<td>
						<input type="text" class="regular-text" id="esp-cta-text"
						       name="esp[cta_text]"
						       value="<?php echo esc_attr( $s['cta_text'] ); ?>">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="esp-cta-note">Button Sub-text</label></th>
					<td>
						<input type="text" class="large-text" id="esp-cta-note"
						       name="esp[cta_note]"
						       value="<?php echo esc_attr( $s['cta_note'] ); ?>">
						<p class="description">Small line below the button, e.g. "No credit card required."</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="esp-save-lbl">Annual Toggle Badge</label></th>
					<td>
						<input type="text" style="width:160px" id="esp-save-lbl"
						       name="esp[annual_savings_label]"
						       value="<?php echo esc_attr( $s['annual_savings_label'] ); ?>">
						<p class="description">e.g. "Save 10%" — shown as a chip on the Annual billing button.</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="esp-accent-color">Accent Color</label></th>
					<td>
						<input type="text" id="esp-accent-color" class="esp-color-picker"
						       name="esp[accent_color]"
						       value="<?php echo esc_attr( $s['accent_color'] ); ?>"
						       data-default-color="#FE5000">
						<p class="description">Applied to buttons, card borders, checkmarks, and highlights. Default: <code>#FE5000</code> (StageStock orange).</p>
					</td>
				</tr>
				<tr>
					<th scope="row">Modal Library</th>
					<td>
						<fieldset>
							<label style="display:block;margin-bottom:6px">
								<input type="radio" name="esp[modal_library]" value="magnific"
								       <?php checked( $s['modal_library'], 'magnific' ); ?>>
								<strong>Magnific Popup</strong>
								<span class="description" style="margin-left:6px">— built into the Agile theme; recommended</span>
							</label>
							<label style="display:block">
								<input type="radio" name="esp[modal_library]" value="fancybox"
								       <?php checked( $s['modal_library'], 'fancybox' ); ?>>
								<strong>FancyBox</strong>
								<span class="description" style="margin-left:6px">— loaded by the Easy FancyBox plugin</span>
							</label>
						</fieldset>
					</td>
				</tr>
			</table>

			<?php submit_button( 'Save Settings' ); ?>
		</form>
	</div>
	<?php
}
