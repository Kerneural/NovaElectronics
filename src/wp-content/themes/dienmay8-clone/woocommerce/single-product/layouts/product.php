<?php
/**
 * Custom Single Product Layout overriding Flatsome's default.
 *
 * @package          Flatsome/WooCommerce/Templates
 * @flatsome-version 3.19.0
 */

?>
<div class="product-container">

<div class="product-main">
	<div class="row content-row mb-0">

		<div class="product-gallery col large-4">
			<?php flatsome_sticky_column_open( 'product_sticky_gallery' ); ?>
			<?php
				/**
				 * woocommerce_before_single_product_summary hook
				 *
				 * @hooked woocommerce_show_product_images - 20
				 */
				do_action( 'woocommerce_before_single_product_summary' );
			?>
			<?php flatsome_sticky_column_close( 'product_sticky_gallery' ); ?>
		</div>
		<div class="product-info summary col-fit col entry-summary <?php flatsome_product_summary_classes();?>">
			<?php
				/**
				 * woocommerce_single_product_summary hook
				 *
				 * @hooked woocommerce_template_single_title - 5
				 * @hooked woocommerce_template_single_rating - 10
				 * @hooked woocommerce_template_single_price - 10
				 * @hooked woocommerce_template_single_excerpt - 20
				 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked woocommerce_template_single_meta - 40
				 * @hooked woocommerce_template_single_sharing - 50
				 */
				do_action( 'woocommerce_single_product_summary' );
			?>
		</div>

		<div id="product-sidebar" class="col large-3 hide-for-medium product-sidebar-small">
			<!-- Widget 1: Why Buy -->
			<div class="col-inner mb-4" style="background-color: #fff; padding: 15px; border-radius: 4px; border: 1px solid #e5e7eb; margin-bottom: 20px;">
				<div class="product_icon" style="margin-left: 0; padding: 0 0 10px 0;">
					<h3 class="product_icon_title" style="margin-bottom: 10px;"><span class="relative">Why buy at DailySmartLife</span></h3>
				</div>
				<ul class="why_product">
					<li class="why_item">
						<div><img src="/wp-content/uploads/2022/11/item1.png" alt=""></div>
						<div class="why_text">Hàng chính hãng 100%</div>
					</li>
					<li class="why_item">
						<div><img src="/wp-content/uploads/2022/11/item2.png" alt=""></div>
						<div class="why_text">Enthusiastic consulting staff</div>
					</li>
					<li class="why_item">
						<div><img src="/wp-content/uploads/2022/11/item3.png" alt=""></div>
						<div class="why_text">Professional installation team</div>
					</li>
					<li class="why_item">
						<div><img src="/wp-content/uploads/2022/11/item4.png" alt=""></div>
						<div class="why_text">Flexible return policy</div>
					</li>
					<li class="why_item">
						<div><img src="/wp-content/uploads/2022/11/item5.png" alt=""></div>
						<div class="why_text">Free shipping to Hanoi</div>
					</li>
					<li class="why_item">
						<div><img src="/wp-content/uploads/2022/11/item7.png" alt=""></div>
						<div class="why_text">Convenient payment</div>
					</li>
				</ul>
			</div>

			<!-- Widget 2: Warehouse System -->
			<div class="col-inner mb-4" style="background-color: #fff; padding: 15px; border-radius: 4px; border: 1px solid #e5e7eb; margin-bottom: 20px;">
				<div class="product_icon" style="margin-left: 0; padding: 0 0 10px 0;">
					<h3 class="product_icon_title" style="margin-bottom: 10px;"><span class="relative">Warehouse system</span></h3>
				</div>
				<div class="showroom_product">
					<div class="showroom_product_item">
						<h4>Headquarters</h4>
						<div class="showroom_tt">
							<img src="/wp-content/uploads/2022/11/pin.svg"> 
							<span>10 Collyer Quay, Ocean Financial Centre, Singapore 049315</span>
						</div>
						<div class="showroom_tt">
							<img src="/wp-content/uploads/2022/11/phone.svg"> 
							<span>0977.986.xxx</span>
						</div>
					</div>
					<div class="showroom_product_item">
						<h4>Warehouse 1: Jurong Port</h4>
						<div class="showroom_tt">
							<img src="/wp-content/uploads/2022/11/pin.svg"> 
							<span>Jurong Port, Singapore</span>
						</div>
						<div class="showroom_tt">
							<img src="/wp-content/uploads/2022/11/phone.svg"> 
							<span>0977.986.xxx</span>
						</div>
					</div>
					<div class="showroom_product_item">
						<h4>Warehouse 2: Orchard Road</h4>
						<div class="showroom_tt">
							<img src="/wp-content/uploads/2022/11/pin.svg"> 
							<span>437 Orchard Rd, Singapore 238878</span>
						</div>
						<div class="showroom_tt">
							<img src="/wp-content/uploads/2022/11/phone.svg"> 
							<span>0977.986.xxx</span>
						</div>
					</div>
				</div>
			</div>

			<!-- Widget 3: Customer Support -->
			<div class="col-inner mb-4" style="background-color: #fff; padding: 15px; border-radius: 4px; border: 1px solid #e5e7eb;">
				<h3 class="single_product_title" style="margin-bottom: 10px; padding: 0 0 10px 0;"><span style="margin-left: 0;">Customer Support</span></h3>
				<div class="suport_kh" style="padding: 0;">
					<div class="suport_kh_item">
						<img src="/wp-content/uploads/2022/11/phone-2.svg" style="width: 24px; margin-right: 8px;"> 
						<div class="suport_kh_item_content" style="margin-left: 0;">
							<span style="font-size: 14px; font-weight: bold; color: rgb(71, 74, 80);">Sales</span> 
							<div class="flex items-center" style="font-size: 14px;">
								<a href="tel:0989072072" style="font-size: 16px; font-weight: bold; color: var(--primary-color);">0989072072</a> 
								<span style="font-size: 12px; color: #6a6a6a;">&nbsp;(7:00 - 20:00)</span>
							</div>
						</div>
					</div> 
					<div class="suport_kh_item">
						<img src="/wp-content/uploads/2022/11/phone-2.svg" style="width: 24px; margin-right: 8px;"> 
						<div class="suport_kh_item_content" style="margin-left: 0;">
							<span style="font-size: 14px; font-weight: bold; color: rgb(71, 74, 80);">Warranty</span> 
							<div class="flex items-center" style="font-size: 14px;">
								<a href="tel:0989072072" style="font-size: 16px; font-weight: bold; color: var(--primary-color);">0989072072</a> 
								<span style="font-size: 12px; color: #6a6a6a;">&nbsp;(8:00 - 20:00)</span>
							</div>
						</div>
					</div> 
					<div class="suport_kh_item">
						<img src="/wp-content/uploads/2022/11/phone-2.svg" style="width: 24px; margin-right: 8px;"> 
						<div class="suport_kh_item_content" style="margin-left: 0;">
							<span style="font-size: 14px; font-weight: bold; color: rgb(71, 74, 80);">Projects/Dealers</span> 
							<div class="flex items-center" style="font-size: 14px;">
								<a href="tel:0989072072" style="font-size: 16px; font-weight: bold; color: var(--primary-color);">0989072072</a> 
								<span style="font-size: 12px; color: #6a6a6a;">&nbsp;(24/7)</span>
							</div>
						</div>
					</div> 
				</div>
			</div>
		</div>

	</div>
</div>

<div class="product-footer">
	<div class="container">
		<?php
			/**
			 * woocommerce_after_single_product_summary hook
			 *
			 * @hooked woocommerce_output_product_data_tabs - 10
			 * @hooked woocommerce_upsell_display - 15
			 * @hooked woocommerce_output_related_products - 20
			 */
			do_action( 'woocommerce_after_single_product_summary' );
		?>
	</div>
</div>
</div>
