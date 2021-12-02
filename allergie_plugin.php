<?php
/*
Plugin Name: Allergie icon
Description: Allergie
Version:     0.1.1
Author:      Xavier Chen
*/


/* css en js function/
/*Css instal met bootstrp eigen style.*/
function Stylesin()
{
    wp_register_style("fontAwesome", "https://use.fontawesome.com/releases/v5.5.0/css/all.css");
    wp_enqueue_style("fontAwesome");
	wp_register_style('XStyle', plugins_url('/allergie.css', __FILE__));
    wp_enqueue_style('XStyle');
    wp_register_style('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');
    wp_enqueue_style('bootstrap');
}

/*Js instal met bootstrp jquery en eigen javascript.*/
function Scriptsin()
{
    wp_enqueue_script('jquery');

    wp_register_script('XScript', plugins_url('/allergie.js', __FILE__));
    wp_enqueue_script('XScript');
    wp_localize_script('XScript', 'ajax_object', ['ajax_url' => admin_url('admin-ajax.php')]);
}

/*Css en Js gebruik in wordpress.*/
function Gostart()
{
    Stylesin();
    Scriptsin();
}

add_action('plugins_loaded', 'Gostart');
/*end css en js */



/* automatisch allergie icon toevoge function */
/* allergie icon en groten icon later zie in elk product */
add_shortcode('allergieImage','getImages');

function getImages()
{
	$gorten = get_field('groten');
    $images = get_field('allergie');

	foreach($images as $image)
    { 	
		?>
		<img src="/wptest/wp-content/uploads/<?php echo $image ?>.png" width="80px", height="80px">  
		<?php	
    }

	
	// if($gorten = "groten")
	// {
	// 	?>
	// 	<div>
	// 	<img src="/wptest/wp-content/uploads/green-energy.png" width="80px", height="80px">  
	// 	</div>
	// 	<?php
	// }else
	// {
	// 	echo "no";
	// }

	
}

/* automatisch voor elk product icon toevoge in product */
add_action('woocommerce_product_meta_end','autoAdd');
function autoAdd()
{
	?>
	<div class="container">
		Allergie:
	<?php
	do_shortcode('[allergieImage]');
	?>
	</div>
	<?php
}
/*end icon later zie */


/*Filter function  */
/*maak de button en checkbox in html */
add_shortcode('filter','setFilter');
function setFilter()
{
	$allergies = ["Gluten","Zwavel","Weekdieren","Melk","Vis","Soja","Sesamzaad","Selderij","Schaald","Pindas","Noten","Mosterd","Lupine","Ei"];



	?>
	<div id="filter-produc" class="container">
	<button class="btn active" id="all-filter"> Show all</button>
	<div class="row">
	<?php
		foreach($allergies as $a)
		{
			?>
			<div  class="col-4">
  				<input type="checkbox" id="<?php echo $a ?>" name="<?php echo $a ?>"  class="filter-btn">
  				<label for="<?php echo $a ?>"><?php echo $a ?></label>
			</div>
			<?php
		}
	?>
	</div>
</div>
<!-- <div class ="row">
  <input  type="checkbox" id="groente" name="groente"  class="filter-btn" value="groente">
  <label for="groente">Groente</label>
</div> -->
	<?php
}


/*result html */
add_shortcode('showProducts','products');
function products()
{
	 ?>
	<div class="container" id="filter-result">

	 <?php
}


/*voro ajax kan gebruik*/
add_action('wp_ajax_my_filter', 'my_filter');
add_action("wp_ajax_nopriv_my_filter", "my_filter");

/*belangrijk filte function*/
/*krijg de naam voor filter door js, daarna stuur zoek de products en terug stuur products html in js.*/
function my_filter()
{
	//get filte name voor js
	$datas = $_POST["data"];
	
	if($datas != null)
	{
		//make wp query zoek dat products niet deze allegie hebben 
		$meta_query =array('relation' => 'AND');
		foreach($datas as $data)
		{

			// if($data == "groten")
			// {
			// 	$meta_query[] =
			// 	[
			// 		'key'     => 'groten',
			// 		'value'   =>  $data,
			// 		'compare' => 'LIKE'
			// 	];
			// }else
			// {
				$meta_query[] =
				[
					'key'     => 'allergie',
					'value'   =>  $data,
					'compare' => 'NOT LIKE'
				];
			// }
			
		}


		$args = array(
			'posts_per_page' => 5,
			'post_status'   => 'publish',
			'post_type' =>'product',
			'meta_query'	=> $meta_query,
		);

		$the_query = new WP_Query( $args );
			
		// Als staad products dan stuur de products html met woccomens doshortcode naar js
		$posts = $the_query->posts;
		if($posts == null)
		{
			?>
			<div class="container text-danger" >
					Jameer geen eten
			</div>
			<?php

		}else
		{
			
			
			$ids=[];
			foreach($posts as $post)
			{
				$ids[]=$post->ID;
			}
			
			$str = join(", ", $ids);
		
			echo do_shortcode('[products ids="'.$str.'"]'); 
		}
		
	}else
	{
		echo do_shortcode('[recent_products per_page="12" columns="4"]');
	}


	wp_die();
}
/*end filter function */





