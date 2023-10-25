<?php
/*
Plugin Name: GHI blogs cards
Description: GHI blogs cards
Version: 1.0
Author: Mustafa slim
Author URI: https://www.linkedin.com/in/mostafa-slim-5ba483127
*/


if (!is_page('blogs')) {
  wp_enqueue_script('blogs-ajax-script', plugin_dir_url(__FILE__) . 'my-script.js', array('jquery'), false, true);

  // in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
  wp_localize_script(
    'blogs-ajax-script',
    'ajax_object',
    array('ajax_url' => admin_url('admin-ajax.php'))
  );
  // Only applies to dashboard panel
}


add_action('wp_ajax_my_action', 'blogs_action');
add_action('wp_ajax_nopriv_my_action', 'blogs_action');

// Same handler function...
function blogs_action()
{
  $jsonString = stripslashes($_POST['filters']);
  $filters = json_decode($jsonString, true);
  $order = $_POST['order'];
  $page = $_POST['page'];

  $meta_query = array('relation' => 'OR');

  $args = array(
    'post_type' => 'blogs',
    'posts_per_page' => 10, // Show all posts
    'paged' => $page,
  );

  if (!empty($order)) {
    $args['meta_key'] = 'date_published';
    $args['order_by'] = 'meta_value';
    $args['order'] = $order;
  }
  // check if filters is not empty 
  if (!empty($filters)) {

    foreach($filters as $key=>$value)
    {
      $meta_query[] = array(
        'key'     => 'keywords',
        'value'   => $value,
        'compare' => 'LIKE',
      );
    }
    $args['meta_query'] =$meta_query;
  }
  wp_send_json(blogs_cards($args));
  // wp_send_json();
  wp_die();
}

function blogs_shortcode()
{

  $fields = get_post_type_object('blogs');
  $group_fields_id = acf_get_field_groups(array('post_type' => 'blogs'))[0]['key'];
  $group_fields = acf_get_fields($group_fields_id);
  $counter = 0;

  /* start filter section */
  echo '<div class="flex flex-col justify-start items-start">';

  foreach ($group_fields as $field) {
    $field_name = $field['name'];
    if ($field_name == 'keywords') {
      $counter++;
      echo   "
      <h3 class='mb-[8px] text-[17px] tracking-[0.1px] leading-[14px]'>Filter</h3>
      <div id='checkbox_filter' class='filter-wrapper overflow-auto'>";
      foreach ($field['choices'] as $key => $choise) {
        echo '<div class="checkbox-wrapper">';
        echo '<input type="checkbox" id="' . $choise . '" name="' . $choise . '" />
        <label for="' . $choise . '">' . $choise . '</label>';
        echo '</div>';
      }
      echo '</div>';
    }
  }

  echo '<select id="select_3" class="mt-[32px] bg-[#860334] text-[white] p-[6px_50px] text-[12px] font-semibold">
        <option value="">Sort Results By</option>
        <option value="DESC">Latest</option>
        <option value="ASC">Oldest</option>
        </select>';
  echo '</div>';
  /* end filter section */

  /* end filter section */

  $args = array(
    'post_type' => 'blogs',
    'posts_per_page' => 10, // Show all posts
    'paged' => get_query_var('paged') ? get_query_var('paged') : 1, //    
    );

  wp_enqueue_style('GhiCardsStyles', '/wp-content/plugins/ghi-blogs/style.css');
  echo '<section id="blogs-section" class="mt-5">';
  $html = blogs_cards($args);
  echo $html['html'];
  echo '</section>';
  // import jQuery script
  echo '<span id="loader" class="loader"></span>';
  if ($html['max_page_number'] > 1) {
    echo '<div>';
    echo '<button class="block mt-3 mx-auto text-[11px] bg-[transparent] p-[4px_16px] font-[500] mt-[14px] rounded-[4px] uppercase border-[1px] border-solid border-[#860334] text-[#860334] transition-all duration-500 hover:bg-[#860334] hover:text-white" id="load-more-button">Load More</button>';
    echo '</div>';
  }
  echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
}

function blogs_cards($args)
{
  $custom_query = new WP_Query($args);
  $response['max_page_number'] = $custom_query->max_num_pages;
  $response['html'] = "";
  $first_post = true;
  if ($custom_query->have_posts()) : $response['html'] .= '
    <div class="container mx-auto">
    <div id="card_container" class="flex justify-center flex-wrap gap-[40px]">';
    while ($custom_query->have_posts()) : $custom_query->the_post();

      $response['html'] .= ' <div class="card relative flex flex-col justify-center p-[15px] bg-[#FFF] border border-[#eee] w-[300px] min-h-[450px] text-[12px] shadow-[#00000019_0px_20px_25px_-5px,#0000_0px_10px_10px_-5px] transition-all hover:scale-[1.02] hover:shadow-2xl rounded-[7px]">';
      if ($first_post) {
        $text = 'Latest</div>';
        $response['html'] .= '<div class="absolute -top-[15px] right-[5px] bg-[#860334] text-white z-30 p-[5px_10px] rounded-[4px]">';
        if (isset($args['order']) && $args['order'] == 'ASC')
          $text = 'Oldest</div>';
        $response['html'] .= $text;
        $first_post = false;
      }
      $response['html'] .= '<div class="lines"></div>
        <div class="content flex flex-col h-[100%]">
          <div class="mt-[10px]">
            <img
              class="h-[158px] w-full bg-cover rounded-[3px]"
              alt=""
              src="' . get_the_post_thumbnail_url(get_the_ID(),) . '"
            />
          </div>
          <div class="flex flex-col bg-[#FFF]">
            <div class="flex flex-col my-1">
              <span class="font-bold text-[16px] mt-[12px] text-[black]">' . get_the_title() . '</span>
              <span class="text-xs pb-2 text-[#888]">
                Free online drawing application for all ages
              </span>
            </div>
            <div class="flex-1 text-[#000] border-t border-b py-3">
            <div
                  class="flex items-center  mb-2 last:mb-0 gap-2"
                >
                  <span class="text-[#860334]"><i class="fa-regular fa-calendar text-[14px]"></i></span>
                  <span class="text-xs">'  . date("F j, Y", strtotime(get_post_meta(get_the_ID(), 'date_published', true))) . '</span>
            </div>
            <div
                  class="flex items-center  mb-2 last:mb-0 gap-2"
                >
                  <span class="text-[#860334]"><i class="fa-solid fa-user text-[13px]"></i></span>
                  <span class="text-xs">'  . get_post_meta(get_the_ID(), 'authors', true) . '</span>
            </div>
            <div
                  class="read-more-content"
                > 
                ' . get_the_content() . '
            </div>
            </div>
          </div>
          <div class="h-[30px] flex items-center text-[10px]">
            <span>active 15 days ago</span>
          </div>
          <a target="_blank" href="' . get_permalink(get_the_ID()) . '" class="bg-[#092140] text-[white] text-center text-[9px] border-[transparent] font-semibold tracking-[0.5px] uppercase padding-[5px_13px] rounded-[6px] hover:text-[white] active:text-[white] focus:text-[white] mt-[auto]">
            Read more
          </a>
        </div>
      </div>';

    endwhile;
    $response['html'] .= '</div>
      </div>';
  endif;
  return $response;
}



add_shortcode('blogs-cards', 'blogs_shortcode');
