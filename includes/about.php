<?php

 ?>
	<section id="about" class="section gray-bg">
		<div class="container">
			<div class="row title text-center">
				<h2 class="margin-top">About</h2>
				<h4 class="light muted">The Annual Ace in the Hole MultiSport Weekend is a legendary event in the Oregon triathlon and running community. It has become a traditional destination race for athletes from across the nation.
					There is something for every level of athletic ability. The weekend includes a first timer triathlon, a sprint, Olympic, and Half-Iron triathlons and 10K and Half marathon runs. Come to experience your first race or come to compete to win, but make sure you come to have fun!
				</h4>
			</div>
		</div>
	</section>
	<section class="section section-padded blue-bg">
		<div class="container">
			<div class="row">
				<div class="col-md-8 col-md-offset-2">
					<!-- #region Jssor Slider Begin -->
					<!-- Source: https://www.jssor.com/pdsnowden/aceinthehole.slider -->
					<script src="js/jquery-1.11.3.min.js" type="text/javascript"></script>
					<script src="js/jssor.slider-23.1.6.mini.js" type="text/javascript"></script>
					<script type="text/javascript">
							jQuery(document).ready(function ($) {

									var jssor_1_options = {
										$AutoPlay: 1,
										$ArrowNavigatorOptions: {
											$Class: $JssorArrowNavigator$
										},
										$BulletNavigatorOptions: {
											$Class: $JssorBulletNavigator$
										}
									};

									var jssor_1_slider = new $JssorSlider$("jssor_1", jssor_1_options);
							});
					</script>
					<style>
							/* jssor slider bullet navigator skin 01 css */
							/*
							.jssorb01 div           (normal)
							.jssorb01 div:hover     (normal mouseover)
							.jssorb01 .av           (active)
							.jssorb01 .av:hover     (active mouseover)
							.jssorb01 .dn           (mousedown)
							*/
							.jssorb01 {
									position: absolute;
							}
							.jssorb01 div, .jssorb01 div:hover, .jssorb01 .av {
									position: absolute;
									/* size of bullet elment */
									width: 12px;
									height: 12px;
									filter: alpha(opacity=70);
									opacity: .7;
									overflow: hidden;
									cursor: pointer;
									border: #000 1px solid;
							}
							.jssorb01 div { background-color: gray; }
							.jssorb01 div:hover, .jssorb01 .av:hover { background-color: #d3d3d3; }
							.jssorb01 .av { background-color: #fff; }
							.jssorb01 .dn, .jssorb01 .dn:hover { background-color: #555555; }

							/* jssor slider arrow navigator skin 03 css */
							/*
							.jssora03l                  (normal)
							.jssora03r                  (normal)
							.jssora03l:hover            (normal mouseover)
							.jssora03r:hover            (normal mouseover)
							.jssora03l.jssora03ldn      (mousedown)
							.jssora03r.jssora03rdn      (mousedown)
							.jssora03l.jssora03ldn      (disabled)
							.jssora03r.jssora03rdn      (disabled)
							*/
							.jssora03l, .jssora03r {
									display: block;
									position: absolute;
									/* size of arrow element */
									width: 55px;
									height: 55px;
									cursor: pointer;
									background: url('img/a03.png') no-repeat;
									overflow: hidden;
							}
							.jssora03l { background-position: -3px -33px; }
							.jssora03r { background-position: -63px -33px; }
							.jssora03l:hover { background-position: -123px -33px; }
							.jssora03r:hover { background-position: -183px -33px; }
							.jssora03l.jssora03ldn { background-position: -243px -33px; }
							.jssora03r.jssora03rdn { background-position: -303px -33px; }
							.jssora03l.jssora03lds { background-position: -3px -33px; opacity: .3; pointer-events: none; }
							.jssora03r.jssora03rds { background-position: -63px -33px; opacity: .3; pointer-events: none; }
					</style>
					<div id="jssor_1" style="position:relative;margin:0 auto;top:0px;left:0px;width:600px;height:300px;overflow:hidden;visibility:hidden;">
							<!-- Loading Screen -->
							<div data-u="loading" style="position:absolute;top:0px;left:0px;background-color:rgba(0,0,0,0.7);">
									<div style="filter: alpha(opacity=70); opacity: 0.7; position: absolute; display: block; top: 0px; left: 0px; width: 100%; height: 100%;"></div>
									<div style="position:absolute;display:block;background:url('img/loading.gif') no-repeat center center;top:0px;left:0px;width:100%;height:100%;"></div>
							</div>
							<div data-u="slides" style="cursor:default;position:relative;top:0px;left:0px;width:600px;height:300px;overflow:hidden;">
									<div>
											<img data-u="image" src="img/slider/above.jpg" />
									</div>
									<div>
											<img data-u="image" src="img/slider/rain.jpg" />
									</div>
									<div>
											<img data-u="image" src="img/slider/stars.jpg" />
									</div>
									<div>
											<img data-u="image" src="img/slider/stick.jpg" />
									</div>
									<div>
											<img data-u="image" src="img/slider/tutu.jpg" />
									</div>
									<a data-u="any" href="https://www.jssor.com/pdsnowden/aceinthehole.slider" style="display:none">bootstrap carousel</a>
							</div>
							<!-- Bullet Navigator -->
							<div data-u="navigator" class="jssorb01" style="bottom:16px;right:16px;" data-autocenter="1">
									<div data-u="prototype" style="width:12px;height:12px;"></div>
							</div>
							<!-- Arrow Navigator -->
							<span data-u="arrowleft" class="jssora03l" style="top:0px;left:8px;width:55px;height:55px;" data-autocenter="2"></span>
							<span data-u="arrowright" class="jssora03r" style="top:0px;right:8px;width:55px;height:55px;" data-autocenter="2"></span>
					</div>
					<!-- #endregion Jssor Slider End -->
				</div>
			</div>
		</div>
	</section>
