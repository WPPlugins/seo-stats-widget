<?php
/*
Plugin Name: SEO Stats Widget
Plugin URI: http://wordpress.org/plugins/seo-stats-widget/
Description: Displays various SEO data about a website like Pagerank, Google backlinks, Google Indexed Pages, Keywords in Google, Keywords Cost, Organic Traffic, SemRush Rank, Alexa Rank, Dmoz Listing etc.
Version: 1.1
Author: Sunny Verma
Author URI: http://99webtools.com
Text Domain: seo-stats-widget
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
include_once (dirname (__FILE__)."/lib/seostatsClass.php");
function seostats_init() {
 load_plugin_textdomain( 'seo-stats-widget', false, dirname( plugin_basename( __FILE__ )) . '/lang/' ); 
}
add_action('init', 'seostats_init');
function seostats_dashboard_widgets()
{
add_meta_box( 'seostats_dashboard_widget', 'SEO Stats', 'seostats_dashboard_widget_function', 'dashboard', 'side', 'high' );
}
add_action( 'wp_dashboard_setup', 'seostats_dashboard_widgets' );

/**
 * Create the function to output the contents of our Dashboard Widget.
 */
function seostats_dashboard_widget_function() {
if($v=get_transient( 'wpseostats' ))
{}
else
{
$o=new SEOStats(site_url());
$v=array('PR'=>$o->get_PR(),'GIP'=>$o->get_GIP(),'GBL'=>$o->get_GBL(),'ALEXA'=>$o->get_Alexa(),'SEM'=>$o->get_SEM());
set_transient('wpseostats',$v,DAY_IN_SECONDS);
}
$dmoz=$v['ALEXA']['dmoz']?'Yes':'No';
echo '<table class="wpseostats">
<tr>
    <td>'.__('Google Pagerank','seo-stats-widget').'</td>
    <td>'.$v['PR'].'</td>
  </tr>
  <tr>
    <td>'.__('Google Indexed Pages','seo-stats-widget').'</td>
    <td>'.$v['GIP'].'</td>
  </tr>
  <tr>
    <td>'.__('Google Backlinks','seo-stats-widget').'</td>
    <td>'.$v['GBL'].'</td>
  </tr>
  <tr>
    <td>'.__('Keywords in Google','seo-stats-widget').'</td>
    <td>'.$v['SEM']['keywords'].'</td>
  </tr>
  <tr>
    <td>'.__('Keywords Cost','seo-stats-widget').'</td>
    <td>'.$v['SEM']['cost'].'$</td>
  </tr>
  <tr>
    <td>'.__('Organic Traffic','seo-stats-widget').'</td>
    <td>'.$v['SEM']['traffic'].'</td>
  </tr>
  <tr>
    <td>'.__('SemRush Rank','seo-stats-widget').'</td>
    <td>'.$v['SEM']['rank'].'</td>
  </tr>
  <tr>
    <td>'.__('Alexa Rank','seo-stats-widget').'</td>
    <td>'.$v['ALEXA']['rank'].'</td>
  </tr>
  <tr>
    <td>'.__('Alexa Backlink','seo-stats-widget').'</td>
    <td>'.$v['ALEXA']['links'].'</td>
  </tr>
  <tr>
    <td>'.__('Dmoz Listing','seo-stats-widget').'</td>
    <td>'.$dmoz.'</td>
  </tr>
  <tr>
    <td>'.__('Website Load time','seo-stats-widget').'</td>
    <td>'.$v['ALEXA']['speed'].' ms</td>
  </tr>
  <tr>
  <td colspan="2">by <a rel="designer" href="http://99webtools.com" class="credit">99webtools</a></td>
  </tr>
</table>';
}
class wpseostats_Widget extends WP_Widget {

	public function __construct() {
	parent::__construct(
	 		'wpseostats_widget',
			'SEO stats Widget',
			array( 'description' => 'SEO Stats Widget' )
		);
		add_action('wp_enqueue_scripts', array(&$this, 'css'));
	}
function css(){

    if ( is_active_widget(false, false, $this->id_base, true) ) {
       wp_enqueue_style( 'seostats', plugins_url( 'style.css', __FILE__ ));
    }           

  }
	public function widget( $args, $instance ) {
	extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
	if($v=get_transient( 'wpseostats' ))
{}
else
{
$o=new SEOStats(site_url());
$v=array('PR'=>$o->get_PR(),'GIP'=>$o->get_GIP(),'GBL'=>$o->get_GBL(),'ALEXA'=>$o->get_Alexa(),'SEM'=>$o->get_SEM());
set_transient('wpseostats',$v,DAY_IN_SECONDS);
}
$dmoz=$v['ALEXA']['dmoz']?'Yes':'No';
echo '<aside class="widget wpseostats"><table>
  <tr>
    <td>'.__('Google Pagerank','seo-stats-widget').'</td>
    <td>'.$v['PR'].'</td>
  </tr>
  <tr>
    <td>'.__('Google Indexed Pages','seo-stats-widget').'</td>
    <td>'.$v['GIP'].'</td>
  </tr>
  <tr>
    <td>'.__('Google Backlinks','seo-stats-widget').'</td>
    <td>'.$v['GBL'].'</td>
  </tr>
  <tr>
    <td>'.__('Keywords in Google','seo-stats-widget').'</td>
    <td>'.$v['SEM']['keywords'].'</td>
  </tr>
  <tr>
    <td>'.__('Keywords Cost','seo-stats-widget').'</td>
    <td>'.$v['SEM']['cost'].'$</td>
  </tr>
  <tr>
    <td>'.__('Organic Traffic','seo-stats-widget').'</td>
    <td>'.$v['SEM']['traffic'].'</td>
  </tr>
  <tr>
    <td>'.__('SemRush Rank','seo-stats-widget').'</td>
    <td>'.$v['SEM']['rank'].'</td>
  </tr>
  <tr>
    <td>'.__('Alexa Rank','seo-stats-widget').'</td>
    <td>'.$v['ALEXA']['rank'].'</td>
  </tr>
  <tr>
    <td>'.__('Alexa Backlink','seo-stats-widget').'</td>
    <td>'.$v['ALEXA']['links'].'</td>
  </tr>
  <tr>
    <td>'.__('Dmoz Listing','seo-stats-widget').'</td>
    <td>'.$dmoz.'</td>
  </tr>
  <tr>
    <td>'.__('Website Load time','seo-stats-widget').'</td>
    <td>'.$v['ALEXA']['speed'].' ms</td>
  </tr>
  <tr>
  <td colspan="2"><a rel="designer" href="http://99webtools.com" class="credit"><img src="'.plugins_url( 'lib/tools.gif' , __FILE__ ).'" alt="SEO Tools"></a></td>
  </tr>
</table>
<aside>';

	}

 	public function form( $instance ) {
		$title = isset($instance[ 'title' ])?$instance[ 'title' ]:__('SEO Stats','seo-stats-widget');
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /><br>
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}
}
add_action('widgets_init',
     create_function('', 'return register_widget("wpseostats_Widget");')
);
function seostats_css() {
   echo '<style type="text/css">
           .wpseostats{width:100%}
		   .wpseostats td{padding:2px}
		   .wpseostats tr:nth-child(odd){background-color:#EFEFEF}
         </style>';
}
add_action('admin_head', 'seostats_css');
?>