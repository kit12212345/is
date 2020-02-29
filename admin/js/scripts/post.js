var post = {
  current_post_status: 'published',
  current_time: 0,
  current_data_post: 0,
  current_edit_item: 0,
  timer_search_ht: null,
  init: function(){
    var now_time = $('#now_time').attr('data-info');
    now_time = JSON.parse(now_time);
    this.current_time = now_time.year + '-' + now_time.month + '-' + now_time.day + ', ' + now_time.hour + ':' + now_time.minut + ':' + now_time.seconds;

    var post_date = $('#post_date').attr('data-info');
    post_date = JSON.parse(post_date);
    this.current_data_post = post_date.year + '-' + post_date.month + '-' + post_date.day + ', ' + post_date.hour + ':' + post_date.minut + ':' + post_date.seconds;

    var post_info = $('#post_info').attr('data-info');
    post_info = JSON.parse(post_info);
    this.current_edit_item = post_info.edit_item;
    this.current_post_status = post_info.post_status;


  },
  search_posts: function() {
    var search_str = $('#post_search_str').val();
    search_str = search_str ? '&search_str=' + search_str : '';
    return window.location.href = '?q=posts' + search_str;
  },
  apply_filter: function(){
    var sort_date = $('#select_post_date').val();
    var sort_cat = $('#select_post_cat').val();

    sort_date = sort_date != 0 ? '&date=' + sort_date : '';
    sort_cat = sort_cat != 0 ? '&cat_id=' + sort_cat : '';

    return window.location.href = '?q=posts' + sort_cat + sort_date;

  },
  selected_all_posts: function(element){
    var checked = $(element).prop('checked');
    return $('.checked_post').prop('checked',checked);
  },
  show_edit: function(element){
    var post_id = $(element).attr('data-postid');

    if($('#post_item_' + post_id).css('display') == 'none'){
      $('#post_item_' + post_id).show();
      $('#c_hide_edit_' + post_id).hide();
    } else{
      $('#post_item_' + post_id).hide();
      $('#c_hide_edit_' + post_id).show();
      $('#i_post_title_' + post_id).focus();

      var name = $('#i_post_title_' + post_id), nameVal = name.val();
      name.val('').focus().val(nameVal);

    }

  },
  small_update: function(element){
    var _this = post;
    var post_id = $(element).attr('data-postid');
    var title = $('#i_post_title_' + post_id).val();
    var alias = $('#i_post_alias_' + post_id).val();

    ajx({
      url: '/admin/ajax/ajax_posts.php',
      method: 'post',
      dataType: 'json',
      data: {
        action: 'small_update',
        post_id: post_id,
        title: title,
        alias: alias
      },
      success: function(data){
        if(data.result == 'true'){
          $('#post_title_' + post_id).text(title);
          return _this.show_edit(element);
        } else{
          alert(data.string);
        }
      },
      error: function(err){
        console.log(err);
      }
    });

  },
  get_selected_posts: function (){
    var posts = [];

    for (var i = 0; i < $('.checked_post:checked').length; i++) {
      var item = $('.checked_post:checked').eq(i);
      var post_id = $(item).val();
      posts.push(post_id);
    }

    return posts;
  },
  group_post_action: function(element){
    var action = $('#s_action_posts').val();
    if(action == 'delete_post') return this._delete(element);
  },
  restore_post: function(element){
    var post_id = $(element).attr('data-postid');

    ajx({
      url: '/admin/ajax/ajax_posts.php',
      method: 'post',
      dataType: 'json',
      data: {
        action: 'restore_post',
        post_id: post_id
      },
      success: function(data){
        if(data.result == 'true'){
          window.location.reload();
        } else{
          alert(data.string);
        }
      },
      error: function(err){
        console.log(err);
      }
    });

  },
  _delete: function(element){
    var post_id = $(element).attr('data-postid');
    var group_delete = $(element).attr('data-group');
    var posts = typeof group_delete !== 'undefined' ? this.get_selected_posts() : false;
    var forever = $(element).attr('data-forever');

    forever = typeof forever !== 'undefined' && forever == 'true' ? 1 : 0;

    ajx({
      url: '/admin/ajax/ajax_posts.php',
      method: 'post',
      dataType: 'json',
      data: {
        action: 'delete_post',
        post_id: post_id,
        posts: posts,
        forever: forever
      },
      success: function(data){
        if(data.result == 'true'){
          window.location.reload();
        } else{
          alert(data.string);
        }
      },
      error: function(err){
        console.log(err);
      }
    });
  },
  add_post: function(element){
    var _this = post;
    var action = $(element).attr('data-action');

    action = action == 'update_post' ? action : 'add_post';

    var title_ru = $('#post_title_ru').val();
    var title_en = $('#post_title_en').val();
    var alias_ru = $('#post_alias_ru').val();
    var alias_en = $('#post_alias_en').val();
    var content_ru = content_post_ru.get_content();;
    var content_en = content_post_en.get_content();;
    var status = $('#post_status').val();
    var image_hash = $('#md5_hash').val();
    var keywords_ru = $('#post_keywords_ru').val();
    var keywords_en = $('#post_keywords_en').val();
    var description_ru = $('#post_description_ru').val();
    var description_en = $('#post_description_en').val();
    var date_pub = this.get_select_date();
    var selected_cats = [];
    var ingredients_ru = [];
    var ingredients_en = [];
    var hash_tags = [];


    // return console.log(content);

    for (var i = 0; i < $('.ing_item_ru').length; i++) {
      var item = $('.ing_item_ru').eq(i);
      var ing_id = $(item).attr('data-ingid');
      var text = $('#img_name_' + ing_id + '_ru').text();
      ingredients_ru.push(text);
    }

    for (var i = 0; i < $('.ing_item_en').length; i++) {
      var item = $('.ing_item_en').eq(i);
      var ing_id = $(item).attr('data-ingid');
      var text = $('#img_name_' + ing_id + '_en').text();
      ingredients_en.push(text);
    }

    for (var i = 0; i < $('.select_tree_cat:checked').length; i++) {
      selected_cats.push($('.select_tree_cat:checked').eq(i).val());
    }

    for (var i = 0; i < $('.ht_item').length; i++) {
      hash_tags.push($('.ht_item').eq(i).attr('data-name'));
    }

    if(!title_ru && !title_en) return alert('Введите заголовок записи');
    if(!content_ru && !content_en) return alert('Введите текст записи');

    date_pub = date_pub.year + '-' + date_pub.month + '-' + date_pub.day + ', ' +
    date_pub.hour + ':' + date_pub.minut + ':' + date_pub.second;

    var c_user_recipe_id = typeof user_recipe_id !== 'undefined' ? user_recipe_id : 0;

    ajx({
      url: '/admin/ajax/ajax_posts.php',
      method: 'post',
      dataType: 'json',
      data: {
        action: action,
        post_id: _this.current_edit_item,
        title_ru: title_ru,
        title_en: title_en,
        content_ru: content_ru,
        content_en: content_en,
        image_hash: image_hash,
        keywords_ru: keywords_ru,
        keywords_en: keywords_en,
        description_ru: description_ru,
        description_en: description_en,
        ingredients_ru: ingredients_ru,
        ingredients_en: ingredients_en,
        alias_ru: alias_ru,
        alias_en: alias_en,
        status: status,
        published_date: date_pub,
        cats: selected_cats,
        hash_tags: hash_tags,
        user_recipe_id: c_user_recipe_id
      },
      success: function(data){
        if(data.result == 'true'){
          window.location.reload();
        } else{
          alert(data.string);
        }
      },
      error: function(err){
        console.log(err);
      }
    });

  },
  add_cat: function(){
    var _this = post;
    var name = $('#n_cat_name').val();
    var parent_id = $('#n_cat_parent').val();

    if($.trim(name) == '') return alert('Введине название рубрики');

    ajx({
      url: '/admin/ajax/ajax_categories.php',
      method: 'post',
      dataType: 'json',
      data: {
        action: 'add_cat',
        name: name,
        parent_id: parent_id,
        create_tree: 1
      },
      success: function(data){
        if(data.result == 'true'){
          data.parent_id = parent_id;
          data.name = name;
          return _this.success_add_cat(data);
        } else{
          alert(data.string);
        }
      },
      error: function(err){
        console.log(err);
      }
    });


  },
  success_add_cat: function(data){
    var parent_id = data.parent_id;
    var name = data.name;

    var exist_cat_list = typeof $('#cats_list_' + parent_id)[0] !== 'undefined' ? true : false;

    var html = '';

    if(exist_cat_list === false){
      html += '<ul id="cats_list_' + parent_id + '">';
    }

    html += '<li id="category_' + data.item_id + '">';
    html += '<label class="selectit">';
    html += '<input checked="checked" value="' + data.item_id + '" type="checkbox" id="in_category_' + data.item_id + '">';
    html += name;
    html += '</label>';
    html += '</li>';

    if(exist_cat_list === false){
      html += '</ul>';
    }

    var insert_content = exist_cat_list === true ? '#cats_list_' + parent_id : '#category_' + parent_id;

    $(insert_content).append(html);

    $('#n_cat_parent').html(data.tree_cats);
    $('#n_cat_parent').val(parent_id);

  },
  get_select_date: function() {
    var month = parseInt($('#mi_month').val()),
    day = parseInt($('#mi_day').val()),
    year = parseInt($('#mi_year').val()),
    hour = parseInt($('#mi_hour').val()),
    minut = parseInt($('#mi_minut').val()),
    second = parseInt($('#mi_second').val());

    hour = isNaN(hour) ? 0 : hour;
    hour = hour < 0 ? 0 : hour;
    hour = hour > 23 ? 23 : hour;

    minut = isNaN(minut) ? 0 : minut;
    minut = minut < 0 ? 0 : minut;
    minut = minut > 59 ? 59 : minut;

    year = isNaN(year) ? 2000 : year;
    year = year < 0 ? 2000 : year;

    month = isNaN(month) ? 1 : month;
    month = month < 1 ? 1 : month;
    month = month > 12 ? 12 : month;

    day = isNaN(day) ? 1 : day;
    day = day < 1 ? 1 : day;

    var month_days = {1: 31, 3: 31, 4: 30, 5: 31, 6: 30, 7: 31, 8: 31, 9: 30, 10: 31, 11: 30, 12: 31};

    if(month == 2){
      if(year % 4 == 0){
        day = day > 29 ? 29 : day;
      } else {
        day = day > 28 ? 28 : day;
      }
    } else {
      day = day > month_days[month] ? month_days[month] : day;
    }

    month = month < 10 ? '0' + month : month;
    day = day < 10 ? '0' + day : day;
    hour = hour < 10 ? '0' + hour : hour;
    minut = minut < 10 ? '0' + minut : minut;
    second = second < 10 ? '0' + second : second;

    $('#mi_month').val(parseInt(month)),
    $('#mi_day').val(day),
    $('#mi_year').val(year),
    $('#mi_hour').val(hour),
    $('#mi_minut').val(minut),
    $('#mi_second').val(second);

    return {
      year: year,
      month: month,
      day: day,
      hour: hour,
      minut: minut,
      second: second
    };
  },
  set_data_post: function(element){
    var select_date = this.get_select_date();

    var conf_date = select_date.year + '-' + select_date.month + '-' + select_date.day + ', ' +
    select_date.hour + ':' + select_date.minut + ':' + select_date.second;


    if(this.current_edit_item){

      var pub_text = conf_date <= this.current_time ? 'Опубликовать:' : 'Планируемая публикация: ';

      var pub_value = select_date.day + '.' + select_date.month + '.' + select_date.year + ', в ' +
      select_date.hour + ':' + select_date.minut;
      $('#publish_title').text(pub_text);
      $('#publish_text').text(pub_value);

    } else{

      if(conf_date != this.current_time){
        var pub_text = 'Запланировать:';

        var pub_value = select_date.day + '.' + select_date.month + '.' + select_date.year + ', в ' +
        select_date.hour + ':' + select_date.minut;

        $('#publish_title').text(pub_text);
        $('#publish_text').text(pub_value);

      } else {

        var pub_value = select_date.day + '.' + select_date.month + '.' + select_date.year + ', в ' +
        select_date.hour + ':' + select_date.minut;

        $('#publish_title').text('Опубликовать :');
        $('#publish_text').text('сразу');

      }


    }


    return hund_event.apply(element);
  },
  save_post_status: function(element){

    var status = $('#post_status').children('option:selected').attr('data-text');
    $('#text_post_status').text(status);

    if(status == ''){

    }


    return hund_event.apply(element);

  },
  init_search_ht: function(e) {
    var _this = post;

    clearTimeout(_this.timer_search_ht);

    _this.timer_search_ht = setTimeout(function() {
      _this.search_ht();
    },500);

  },
  search_ht: function(){
    var ht_name = $('#wr_hash_tags').val();
    ht_name = ht_name.split(',');
    ht_name = ht_name[ht_name.length - 1];

    $('#pi_add_ht').addClass('load_ht');

    ajx({
      url: '/admin/ajax/ajax_posts.php',
      method: 'post',
      dataType: 'json',
      data: {
        action: 'search_ht',
        name: ht_name
      },
      success: function(data){
        console.log(data);
        if(data.result == 'true'){

          if(data.exist_ht){
            $('#ht_search_result').html(data.html);
            $('#ht_search_result').show();
          } else{
            $('#ht_search_result').hide();
          }

        } else{
          alert(data.string);
        }
        $('#pi_add_ht').removeClass('load_ht');
      },
      error: function(err){
        console.log(err);
        $('#pi_add_ht').removeClass('load_ht');
      }
    });

  },
  select_search_ht: function(element){
    var ht_value = $('#wr_hash_tags').val();
    var ht_name = $(element).attr('data-name');
    ht_name += ', ';

    ht_value = ht_value.split(',');

    ht_value[ht_value.length - 1] = ht_name;

    var new_value = ht_value.join(',');

    $('#ht_search_result').hide();

    $('#wr_hash_tags').val(new_value);
    $('#wr_hash_tags').focus();

  },
  add_hash_tags: function(){
    var ht_value = $('#wr_hash_tags').val();

    ht_value = ht_value.split(',');

    for (var i = 0; i < ht_value.length; i++) {
      var ht = '';
      var ht_name = $.trim(ht_value[i]);

      if(ht_name == '') continue;
      if($('.ht_item[data-name="' + ht_name + '"]').length > 0) continue;

      ht += '<div class="relative ht_item" data-name="' + ht_name + '">';
        ht += '<div class="absolute delete_ht" onclick="post.delete_hash_tag(this);">×</div>';
        ht += '<div class="ht_name">' + ht_name + '</div>';
      ht += '</div>';

      $('#ht_items').append(ht);

    }

    $('#wr_hash_tags').val('');

  },
  delete_hash_tag: function(element){
    return $(element).parent().remove();
  }
};
