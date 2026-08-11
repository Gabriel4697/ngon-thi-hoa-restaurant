<?php
/**
 * Data import functions - migrates blog posts, menu items, job positions
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Import blog posts from ngonthihoarestaurant.com
 */
function nth_import_blog_posts() {
    $posts = [
        [
            'title'   => 'Khám Phá Ẩm Thực Đà Nẵng Tại Ngon Thị Hoa',
            'slug'    => 'kham-pha-am-thuc-da-nang',
            'excerpt' => 'Đà Nẵng không chỉ nổi tiếng với những bãi biển đẹp mà còn được biết đến với nền ẩm thực phong phú, đa dạng.',
            'content' => '<p>Đà Nẵng không chỉ nổi tiếng với những bãi biển đẹp mà còn được biết đến với nền ẩm thực phong phú, đa dạng. Nhà hàng Ngon Thị Hoa tự hào mang đến những trải nghiệm ẩm thực đích thực nhất của vùng đất này.</p>
<h2>Những Món Ăn Đặc Trưng</h2>
<p>Từ bát mì Quảng thơm ngon đến những con tôm hùm tươi sống, mỗi món ăn tại Ngon Thị Hoa đều được chế biến với tâm huyết và tình yêu của đội ngũ đầu bếp tay nghề cao.</p>
<p>Không gian nhà hàng xanh mát với nhiều cây xanh tạo cảm giác thư giãn, thoải mái cho thực khách khi dùng bữa.</p>',
            'date'    => '2024-07-15',
            'author'  => 'Ngon Thị Hoa',
        ],
        [
            'title'   => 'Top 10 Món Ăn Sáng Ngon Nhất Tại Ngon Thị Hoa',
            'slug'    => 'top-10-mon-an-sang',
            'excerpt' => 'Bữa sáng là bữa ăn quan trọng nhất trong ngày. Hãy cùng khám phá 10 món ăn sáng được yêu thích nhất tại Ngon Thị Hoa.',
            'content' => '<p>Bữa sáng là bữa ăn quan trọng nhất trong ngày. Hãy cùng khám phá 10 món ăn sáng được yêu thích nhất tại Ngon Thị Hoa.</p>
<h2>1. Bánh Mì Pâté</h2>
<p>Bánh mì giòn rụm với nhân pâté đậm đà, rau sống tươi ngon và nước sốt đặc biệt của nhà hàng.</p>
<h2>2. Phở Bò Đặc Biệt</h2>
<p>Nước dùng được ninh từ xương bò trong 8 tiếng, đậm đà hương vị truyền thống.</p>
<h2>3. Bún Bò Huế</h2>
<p>Món ăn đặc trưng miền Trung với nước dùng cay nồng, thịt bò mềm.</p>',
            'date'    => '2024-06-20',
            'author'  => 'Ngon Thị Hoa',
        ],
        [
            'title'   => 'Không Gian Tropical Garden - Điểm Đến Lý Tưởng',
            'slug'    => 'khong-gian-tropical-garden',
            'excerpt' => 'Không gian Tropical Garden tại Ngon Thị Hoa mang lại trải nghiệm ẩm thực độc đáo giữa thiên nhiên xanh mát.',
            'content' => '<p>Điểm đặc biệt của Ngon Thị Hoa chính là không gian Tropical Garden - khu vườn nhiệt đới xanh mát ngay giữa lòng thành phố Đà Nẵng.</p>
<h2>Thiết Kế Độc Đáo</h2>
<p>Với hơn 200 loại cây xanh và hoa nhiệt đới, không gian nhà hàng tạo cảm giác như đang dùng bữa giữa khu vườn bí ẩn.</p>
<h2>Âm Nhạc Live</h2>
<p>Vào các tối cuối tuần, nhà hàng tổ chức biểu diễn âm nhạc live với các thể loại jazz, acoustic mang lại trải nghiệm thư giãn tuyệt vời.</p>',
            'date'    => '2024-05-10',
            'author'  => 'Ngon Thị Hoa',
        ],
        [
            'title'   => 'Thực Đơn Mùa Hè 2024 - Tươi Mát Và Hấp Dẫn',
            'slug'    => 'thuc-don-mua-he-2024',
            'excerpt' => 'Chào đón mùa hè 2024, Ngon Thị Hoa ra mắt thực đơn mới với nhiều món ăn và thức uống tươi mát, đặc biệt.',
            'content' => '<p>Chào đón mùa hè 2024, Ngon Thị Hoa ra mắt thực đơn mới với nhiều món ăn và thức uống tươi mát, đặc biệt phù hợp với thời tiết nóng bức của Đà Nẵng.</p>
<h2>Đồ Uống Mới</h2>
<p>Nước ép trái cây tươi, sinh tố trái cây nhiệt đới, và các loại trà thảo mộc đặc biệt.</p>
<h2>Món Ăn Mới</h2>
<p>Gỏi hải sản tươi, cá hấp gừng hành, và nhiều món salad sáng tạo.</p>',
            'date'    => '2024-04-25',
            'author'  => 'Ngon Thị Hoa',
        ],
        [
            'title'   => 'Ẩm Thực Việt Nam Qua Góc Nhìn Của Đầu Bếp',
            'slug'    => 'am-thuc-viet-qua-goc-nhin-dau-bep',
            'excerpt' => 'Bếp trưởng của Ngon Thị Hoa chia sẻ về triết lý nấu ăn và tình yêu với ẩm thực Việt Nam.',
            'content' => '<p>Đầu bếp tại Ngon Thị Hoa không chỉ là người nấu ăn, họ là những người kể chuyện bằng món ăn.</p>
<h2>Triết Lý Nấu Ăn</h2>
<p>"Mỗi món ăn là một câu chuyện về văn hóa, về con người Việt Nam. Chúng tôi muốn mỗi thực khách khi thưởng thức món ăn của mình đều cảm nhận được tình yêu và tâm huyết đó."</p>
<h2>Nguyên Liệu Tươi Sạch</h2>
<p>100% nguyên liệu được chọn lọc từ các nhà cung cấp uy tín, đảm bảo tươi sạch và an toàn cho sức khỏe.</p>',
            'date'    => '2024-03-15',
            'author'  => 'Ngon Thị Hoa',
        ],
        [
            'title'   => 'Sự Kiện Đặc Biệt: Tết Đoan Ngọ Tại Ngon Thị Hoa',
            'slug'    => 'su-kien-tet-doan-ngo',
            'excerpt' => 'Nhân dịp Tết Đoan Ngọ, Ngon Thị Hoa tổ chức sự kiện ẩm thực đặc biệt với nhiều hoạt động thú vị.',
            'content' => '<p>Tết Đoan Ngọ (5/5 âm lịch) là dịp Ngon Thị Hoa tổ chức các hoạt động văn hóa ẩm thực đặc sắc.</p>
<h2>Thực Đơn Đặc Biệt</h2>
<p>Cơm rượu, bánh ú tro, và nhiều món ăn truyền thống đặc trưng của ngày Tết Đoan Ngọ.</p>
<h2>Hoạt Động Thú Vị</h2>
<p>Trải nghiệm làm bánh ú tro cùng đầu bếp, tìm hiểu về phong tục tập quán Tết Đoan Ngọ của người Việt.</p>',
            'date'    => '2024-02-10',
            'author'  => 'Ngon Thị Hoa',
        ],
    ];

    $imported = 0;
    foreach ( $posts as $post_data ) {
        if ( get_page_by_path($post_data['slug'], OBJECT, 'post') ) continue;

        $post_id = wp_insert_post([
            'post_type'    => 'post',
            'post_title'   => $post_data['title'],
            'post_name'    => $post_data['slug'],
            'post_content' => $post_data['content'],
            'post_excerpt' => $post_data['excerpt'],
            'post_status'  => 'publish',
            'post_date'    => $post_data['date'] . ' 08:00:00',
            'post_author'  => 1,
        ]);

        if ( ! is_wp_error($post_id) ) $imported++;
    }

    return "Đã import $imported bài viết blog.";
}

/**
 * Import sample menu items
 */
function nth_import_menu_items() {
    $items = [
        // Sáng
        ['Bánh Mì Pâté',         'sang', 'Bánh mì giòn rụm với nhân pâté', 'Crispy baguette with pâté filling', 25000],
        ['Xôi Gà',               'sang', 'Xôi dẻo với thịt gà xé', 'Sticky rice with shredded chicken', 35000],
        ['Phở Bò Đặc Biệt',      'sang', 'Phở bò ninh xương 8 tiếng', 'Beef pho with 8-hour bone broth', 65000],
        ['Bún Bò Huế',           'sang', 'Bún bò cay đặc trưng Huế', 'Spicy Hue-style beef noodle', 55000],
        ['Bánh Cuốn Thịt',       'sang', 'Bánh cuốn mỏng mềm với nhân thịt', 'Steamed rice rolls with pork filling', 45000],
        // Trưa & Tối
        ['Cơm Chiên Cá Mặn',     'trua_toi', 'Cơm chiên với cá mặn đặc trưng', 'Fried rice with salted fish', 75000],
        ['Gà Nướng Lá Chanh',    'trua_toi', 'Gà nướng thơm với lá chanh', 'Grilled chicken with kaffir lime leaves', 180000],
        ['Hải Sản Xào Rau',      'trua_toi', 'Hải sản tươi xào rau củ', 'Fresh seafood stir-fried with vegetables', 220000],
        ['Lẩu Thái Hải Sản',     'trua_toi', 'Lẩu chua cay Thái với hải sản', 'Thai-style spicy seafood hot pot', 350000],
        ['Tôm Hùm Hấp Bia',      'trua_toi', 'Tôm hùm hấp với bia và gừng', 'Beer-steamed lobster with ginger', 'market'],
        // Đồ Uống
        ['Nước Ép Dưa Hấu',      'do_uong', 'Nước ép dưa hấu tươi mát', 'Fresh watermelon juice', 35000],
        ['Sinh Tố Xoài',         'do_uong', 'Sinh tố xoài chín thơm ngon', 'Ripe mango smoothie', 45000],
        ['Trà Đào Cam Sả',       'do_uong', 'Trà với đào, cam và sả tươi', 'Peach, orange & lemongrass tea', 40000],
        ['Cà Phê Sữa Đá',        'do_uong', 'Cà phê phin truyền thống với sữa đặc', 'Vietnamese iced milk coffee', 30000],
        // Đồ Uống Có Cồn
        ['Bia Tiger',            'do_uong_co_con', 'Bia Tiger lạnh', 'Cold Tiger beer', 25000],
        ['Whisky Johnnie Walker', 'do_uong_co_con', 'Johnnie Walker Black Label', 'Johnnie Walker Black Label', 120000],
        // Rượu Vang
        ['Vang Đỏ Pháp',         'ruou_vang', 'Rượu vang đỏ Pháp - Bordeaux', 'French red wine - Bordeaux', 650000],
        ['Vang Trắng Ý',         'ruou_vang', 'Rượu vang trắng Ý - Pinot Grigio', 'Italian white wine - Pinot Grigio', 550000],
        ['Champagne Moët',       'ruou_vang', 'Champagne Moët & Chandon', 'Moët & Chandon Champagne', 2800000],
    ];

    $imported = 0;
    foreach ( $items as [$title, $group, $desc_vi, $desc_en, $price] ) {
        $existing = get_posts(['post_type'=>'menu_item','name'=>sanitize_title($title),'numberposts'=>1]);
        if ( $existing ) continue;

        $post_id = wp_insert_post([
            'post_type'    => 'menu_item',
            'post_title'   => $title,
            'post_content' => $desc_vi,
            'post_excerpt' => $desc_vi,
            'post_status'  => 'publish',
        ]);

        if ( ! is_wp_error($post_id) ) {
            update_post_meta( $post_id, '_menu_description_vi', $desc_vi );
            update_post_meta( $post_id, '_menu_description_en', $desc_en );
            if ( $price !== 'market' ) {
                update_post_meta( $post_id, '_menu_price', number_format($price) );
            }
            // Assign to menu group taxonomy
            $term = get_term_by('slug', $group, 'menu_group');
            if ($term) wp_set_post_terms( $post_id, [$term->term_id], 'menu_group' );
            $imported++;
        }
    }

    return "Đã import $imported món ăn vào menu.";
}

/**
 * Import sample job positions
 */
function nth_import_job_positions() {
    $jobs = [
        [
            'title'        => 'Đầu Bếp Chính (Head Chef)',
            'content'      => '<p>Chúng tôi đang tìm kiếm một Đầu bếp chính tài năng và đam mê ẩm thực để gia nhập đội ngũ của Ngon Thị Hoa. Bạn sẽ chịu trách nhiệm xây dựng và phát triển thực đơn, quản lý bếp và đào tạo nhân viên.</p>',
            'requirements' => "- Tốt nghiệp trường nấu ăn hoặc có ít nhất 3 năm kinh nghiệm\n- Am hiểu ẩm thực Việt Nam và ẩm thực quốc tế\n- Kỹ năng lãnh đạo và quản lý tốt\n- Sáng tạo và có khả năng phát triển món mới",
            'type'         => 'full-time',
            'salary'       => '15.000.000 – 25.000.000 VND',
        ],
        [
            'title'        => 'Nhân Viên Phục Vụ (Waiter/Waitress)',
            'content'      => '<p>Ngon Thị Hoa cần tuyển nhân viên phục vụ nhiệt tình, năng động để phục vụ khách hàng trong không gian nhà hàng sang trọng.</p>',
            'requirements' => "- Ngoại hình ưa nhìn, giao tiếp tốt\n- Tiếng Anh cơ bản (ưu tiên có kinh nghiệm)\n- Thái độ phục vụ chuyên nghiệp\n- Sẵn sàng làm ca tối và cuối tuần",
            'type'         => 'full-time',
            'salary'       => '5.000.000 – 8.000.000 VND + tips',
        ],
        [
            'title'        => 'Nhân Viên Pha Chế (Bartender)',
            'content'      => '<p>Tìm kiếm Bartender sáng tạo, có kiến thức về cocktail và đồ uống để làm việc tại quầy bar của nhà hàng.</p>',
            'requirements' => "- Có bằng cấp hoặc chứng chỉ pha chế\n- Tối thiểu 1 năm kinh nghiệm\n- Sáng tạo, có khả năng sáng tác cocktail mới\n- Phong cách chuyên nghiệp",
            'type'         => 'full-time',
            'salary'       => '8.000.000 – 12.000.000 VND',
        ],
    ];

    $imported = 0;
    foreach ( $jobs as $job ) {
        $existing = get_posts(['post_type'=>'job_position','s'=>$job['title'],'numberposts'=>1]);
        if ( $existing ) continue;

        $post_id = wp_insert_post([
            'post_type'    => 'job_position',
            'post_title'   => $job['title'],
            'post_content' => $job['content'],
            'post_status'  => 'publish',
        ]);

        if ( ! is_wp_error($post_id) ) {
            update_post_meta( $post_id, '_job_requirements', $job['requirements'] );
            update_post_meta( $post_id, '_job_type',         $job['type'] );
            update_post_meta( $post_id, '_job_salary',       $job['salary'] );
            $imported++;
        }
    }

    return "Đã import $imported vị trí tuyển dụng.";
}
