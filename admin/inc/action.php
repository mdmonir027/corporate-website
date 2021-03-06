<?php

header('content-type:application/json');

require_once '../../vendor/autoload.php';

use App\Classes\Sliders;
use App\Classes\Works;
use App\Classes\AdminExtras;
use App\Classes\Team;
use App\Classes\Service;
use App\Classes\Testimonials;
use App\Classes\Skills;
use App\Classes\Option;
use App\Classes\Auth;
use App\Classes\Client;


$sliders = new Sliders();
$works = new Works();
$adminExtras = new AdminExtras();
$team = new Team();
$service = new Service();
$testimonial = new Testimonials();
$skills = new Skills();
$options = new Option();
$auth = new Auth();
$client = new Client();


$data = ['error' => false, 'r_url_con' => false];


if (isset($_POST['action']) && $_POST['action'] == 'save-slider') {

    if (isset($_POST['title']) && isset($_POST['sub_title']) && isset($_POST['start_date']) && isset($_POST['end_date']) && isset($_POST['url']) && isset($_POST['status']) && !empty($_FILES['image']['name'])) {

        $title = $_POST['title'];
        $sub_title = $_POST['sub_title'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $url = $_POST['url'];
        $status = $_POST['status'];

//        images upload
        $image = $_FILES['image'];

        $imageName = $image['name'];
        /** @var  $imageExe */
        $imageExe = explode('.', $imageName);
        $imageExe = end($imageExe);

        $imageNameToStore = uniqid() . rand(111111, 999999) . '.' . $imageExe;

        $save = $sliders->save_slider($title, $sub_title, $start_date, $end_date, $url, $status, $imageNameToStore);
        if ($save) {
            move_uploaded_file($image['tmp_name'], '../../uploads/sliders/' . $imageNameToStore);
            $data['message'] = 'Slider Save Success';

        } else {
            $data['error'] = true;
            $data['message'] = 'Slider Save failed';
        }
    } else {

        $data['error'] = true;

        $title = $_POST['title'];
        $sub_title = $_POST['sub_title'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $url = $_POST['url'];
        $status = $_POST['status'];
        $image = $_FILES['image'];

        if ($title == '') {
            $data['message'] = $sliders->slider_error_message('title');
        } elseif ($sub_title == '') {
            $data['message'] = $sliders->slider_error_message('sub title');
        } elseif ($start_date == '') {
            $data['message'] = $sliders->slider_error_message('start date');
        } elseif ($end_date == '') {
            $data['message'] = $sliders->slider_error_message('end date');
        } elseif ($url == '') {
            $data['message'] = $sliders->slider_error_message('url');
        } elseif (empty($_FILES['image']['name'])) {
            $data['message'] = 'Please select a image!';
        } elseif ($status == '') {
            $data['message'] = $sliders->slider_error_message('status');
        } else {
            $data['message'] = 'Something Went Wrong!';
        }
    }

    echo json_encode($data);

}

if (isset($_POST['action']) && $_POST['action'] == 'slider-delete') {


    $id = (int)($_POST['id']);

    $slider = $sliders->slider($id);
    $result = $sliders->slider_delete($id);

    if ($result) {

        $slider_row = $slider->fetch_assoc();

        unlink('../../uploads/sliders/' . $slider_row['image']);

        $data['message'] = 'Slider deleted successfully!';

    } else {
        $data['error'] = 'true';
        $data['message'] = 'Slider deleted failed!';
    }

    echo json_encode($data);

}

if (isset($_POST['action']) && $_POST['action'] == 'update-slider') {

    if (isset($_POST['slider_data']) && isset($_POST['title']) && isset($_POST['sub_title']) && isset($_POST['start_date']) && isset($_POST['end_date']) && isset($_POST['url']) && isset($_POST['status'])) {

        $title = $_POST['title'];
        $sub_title = $_POST['sub_title'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $url = $_POST['url'];
        $status = $_POST['status'];

//        slider id
        $id = base64_decode($_POST['slider_data']);
        $id = (int)($id);

        if ($sliders->slider_find($id)) {

            // slider update with id;

            if ($_FILES['image']['name']) {

                $image = $_FILES['image'];

                $imageName = $image['name'];
                /** @var  $imageExe */
                $imageExe = explode('.', $imageName);
                $imageExe = end($imageExe);

                $image_name = uniqid() . rand(111111, 999999) . '.' . $imageExe;

                $old_image_file = '../../uploads/sliders/' . $sliders->slider_find($id)['image'];
                file_exists($old_image_file) ? unlink($old_image_file) : false;
            } else {
                $image_name = $sliders->slider_find($id)['image'];
            }

            $update = $sliders->slider_update($title, $sub_title, $start_date, $end_date, $url, $status, $image_name, $id);

            if ($update) {
                $data['message'] = 'Slider updated successfully!';
                $_FILES['image']['name'] ? move_uploaded_file($image['tmp_name'], '../../uploads/sliders/' . $image_name) : null;

            } else {
                $data['error'] = 'true';
                $data['message'] = 'Slider updated failed!';
            }

        }

//        echo json_encode($data);

//        images upload


    } else {

        $data['error'] = true;

        $title = $_POST['title'];
        $sub_title = $_POST['sub_title'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $url = $_POST['url'];
        $status = $_POST['status'];
        $image = $_FILES['image'];

        if ($title == '') {
            $data['message'] = $sliders->slider_error_message('title');
        } elseif ($sub_title == '') {
            $data['message'] = $sliders->slider_error_message('sub title');
        } elseif ($start_date == '') {
            $data['message'] = $sliders->slider_error_message('start date');
        } elseif ($end_date == '') {
            $data['message'] = $sliders->slider_error_message('end date');
        } elseif ($url == '') {
            $data['message'] = $sliders->slider_error_message('url');
        } elseif ($status == '') {
            $data['message'] = $sliders->slider_error_message('status');
        } else {
            $data['message'] = 'Something Went Wrong!';
        }
    }

    echo json_encode($data);


}
// slider update
if (isset($_POST['action']) && $_POST['action'] == 'slider-status-change') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $status_update = $sliders->slider_status_update($id, $status);

    if ($status_update) {
        $data['message'] = 'Status updated successfully!';
    } else {
        $data['error'] = 'true';
        $data['message'] = 'Status updated failed!';
    }
    echo json_encode($data);

}


if (isset($_POST['action']) && $_POST['action'] == 'add-work-menu') {

    if (isset($_POST['name']) && $_POST['name'] != '' && isset($_POST['status'])) {
        $name = $_POST['name'];
        $status = $_POST['status'];


        $add_menu = $works->save_menu($name, $status);


        if ($add_menu) {
            $data['message'] = 'Works added successfully!';
        } else {
            $data['error'] = 'true';
            $data['message'] = 'Works added failed!';
        }
    } else {
        $data['error'] = 'true';
        $data['message'] = $works->error_message('name');
    }

    echo json_encode($data);

}
// work menu status update
if (isset($_POST['action']) && $_POST['action'] == 'works-menu-status') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $status_update = $works->works_status_update($id, $status);

    if ($status_update) {
        $data['message'] = 'Status updated successfully!';
    } else {
        $data['error'] = 'true';
        $data['message'] = 'Status updated failed!';
    }
    echo json_encode($data);

}
// work menu update
if (isset($_POST['action']) && $_POST['action'] == 'update-work-menu') {


    if (isset($_POST['name']) && $_POST['name'] != '' && isset($_POST['status']) && isset($_POST['menu_id'])) {
        $name = $_POST['name'];
        $status = $_POST['status'];

        $id = $_POST['menu_id'];
        $id = base64_decode($id);
        $id = (int)$id;

        $add_menu = $works->work_menu_update($name, $status, $id);


        if ($add_menu) {
            $data['message'] = 'Works updated successfully!';
        } else {
            $data['error'] = 'true';
            $data['message'] = 'Works updated failed!';
        }
    } else {
        $data['error'] = 'true';
        $data['message'] = $works->error_message('name');
    }

    echo json_encode($data);

}

if (isset($_POST['action']) && $_POST['action'] == 'works-menu-delete') {

    $id = (int)$_POST['id'];

    $delete = $works->works_menu_delete($id);

    if ($delete) {
        $data['message'] = 'Work menu deleted successfully!';

    } else {
        $data['error'] = 'true';
        $data['message'] = 'Work menu deleted failed!';
    }

    echo json_encode($data);

}


if (isset($_POST['action']) && $_POST['action'] == 'add-work-item') {

    if (isset($_POST['title']) && isset($_POST['menu_id']) && isset($_POST['status']) && !empty($_FILES['image']['name'])) {

        $title = $_POST['title'];
        $menu_id = $_POST['menu_id'];
        $status = $_POST['status'];

//        images upload
        $image = $_FILES['image'];

        $imageName = $image['name'];
        /** @var  $imageExe */
        $imageExe = explode('.', $imageName);
        $imageExe = end($imageExe);

        $imageNameToStore = uniqid() . rand(111111, 999999) . '.' . $imageExe;

        $save = $works->save_work_item($title, $menu_id, $status, $imageNameToStore);
        if ($save) {
            move_uploaded_file($image['tmp_name'], '../../uploads/works/' . $imageNameToStore);
            $data['message'] = 'Works item save Success';
            $data['r_url'] = 'works-items.php';

        } else {
            $data['error'] = true;
            $data['message'] = 'Works item save failed';
        }
    } else {

        $data['error'] = true;

        $title = $_POST['title'];
        $status = $_POST['status'];
        $image = $_FILES['image'];

        if ($title == '') {
            $data['message'] = $sliders->slider_error_message('title');
        } elseif (empty($_FILES['image']['name'])) {
            $data['message'] = 'Please select a image!';
        } elseif ($status == '') {
            $data['message'] = $sliders->slider_error_message('status');
        } else {
            $data['message'] = 'Something Went Wrong!';
        }
    }

    echo json_encode($data);

}

if (isset($_POST['action']) && $_POST['action'] == 'works-item-status-change') {

    $id = $_POST['id'];
    $status = $_POST['status'];

    $status_update = $works->works_item_status_update($id, $status);

    if ($status_update) {
        $data['message'] = 'Status updated successfully!';
    } else {
        $data['error'] = 'true';
        $data['message'] = 'Status updated failed!';
    }
    echo json_encode($data);

}

if (isset($_POST['action']) && $_POST['action'] == 'works-item-remove') {

    $id = (int)($_POST['id']);
    $item = $works->work_item_find($id);

    if ($item->num_rows > 0) {

        $delete = $works->works_item_delete($id);

        if ($delete) {

            $item_row = $item->fetch_assoc();

            unlink('../../uploads/works/' . $item_row['image']);

            $data['message'] = 'Item deleted successfully!';

        } else {
            $data['error'] = 'true';
            $data['message'] = 'Item deleted failed!';
        }
    } else {
        $data['error'] = 'true';
        $data['message'] = 'Item not found!';
    }


    echo json_encode($data);

}

if (isset($_POST['action']) && $_POST['action'] == 'edit-work-item') {

    if (isset($_POST['title']) && isset($_POST['menu_id']) && isset($_POST['status']) && isset($_POST['data_id'])) {


        $id = $_POST['data_id'];
        $id = base64_decode($id);
        $id = (int)$id;

        $item_array = $works->work_item_find($id);
        if ($item_array->num_rows > 0) {

            $item_row = $item_array->fetch_assoc();

            $title = $_POST['title'];
            $menu_id = $_POST['menu_id'];
            $status = $_POST['status'];

            if ($_FILES['image']['name']) {
                $image = $_FILES['image'];

                $imageName = $image['name'];
                /** @var  $imageExe */
                $imageExe = explode('.', $imageName);
                $imageExe = end($imageExe);

                $imageNameToStore = uniqid() . rand(111111, 999999) . '.' . $imageExe;

            } else {
                $imageNameToStore = $item_row['image'];
            }

            $update = $works->update_work_item($title, $menu_id, $status, $imageNameToStore, $id);
            if ($update) {
                if ($_FILES['image']['name']) {
                    unlink('../../uploads/works/' . $item_row['image']);
                    move_uploaded_file($image['tmp_name'], '../../uploads/works/' . $imageNameToStore);
                }
                $data['message'] = 'Works item save Success';
                $data['r_url'] = 'works-items.php';

            } else {
                $data['error'] = true;
                $data['message'] = 'Works item save failed';
            }
        } else {

            $data['error'] = true;

            $title = $_POST['title'];
            $status = $_POST['status'];
            $image = $_FILES['image'];

            if ($title == '') {
                $data['message'] = $sliders->slider_error_message('title');
            } elseif (empty($_FILES['image']['name'])) {
                $data['message'] = 'Please select a image!';
            } elseif ($status == '') {
                $data['message'] = $sliders->slider_error_message('status');
            } else {
                $data['message'] = 'Something Went Wrong!';
            }
        }
    }

//        images upload


    echo json_encode($data);


}


if (isset($_POST['action']) && $_POST['action'] == 'save-about-us') {

    if (isset($_POST['title']) && isset($_POST['sub_title']) && isset($_POST['description'])) {

        $title = $_POST['title'];
        $sub_title = $_POST['sub_title'];
        $description = $_POST['description'];

        $result = $adminExtras->about_us_save($title, $sub_title, $description);

        if ($result) {
            $data['message'] = 'About Info Save Success';

        } else {
            $data['error'] = true;
            $data['message'] = 'About Info save failed';
        }


    } else {
        $data['error'] = true;


        $title = $_POST['title'];
        $sub_title = $_POST['sub_title'];
        $description = $_POST['description'];

        if ($title == '') {
            $data['message'] = $sliders->slider_error_message('title');
        } elseif ($sub_title == '') {
            $data['message'] = $sliders->slider_error_message('sub title');
        } elseif ($description == '') {
            $data['message'] = $sliders->slider_error_message('description');
        } else {
            $data['message'] = 'Something Went Wrong!';
        }
    }

    echo json_encode($data);
}


if (isset($_POST['action']) && $_POST['action'] == 'add-team-member') {

    if (isset($_POST['name']) && isset($_POST['role']) && isset($_POST['short_desc']) && $_FILES['image']['name']) {

        $name = $_POST['name'];
        $role = $_POST['role'];
        $short_desc = $_POST['short_desc'];
        $status = $_POST['status'];

        $facebook = $_POST['facebook'];
        $twitter = $_POST['twitter'];
        $instagram = $_POST['instagram'];
        $linkedIn = $_POST['linkedIn'];

//        images upload
        $image = $_FILES['image'];

        $imageName = $image['name'];
        /** @var  $imageExe */
        $imageExe = explode('.', $imageName);
        $imageExe = end($imageExe);

        $imageNameToStore = uniqid() . rand(111111, 999999) . '.' . $imageExe;

        $result = $team->add_member($name, $role, $short_desc, $status, $facebook, $twitter, $instagram, $linkedIn, $imageNameToStore);
        if ($result) {
            move_uploaded_file($image['tmp_name'], '../../uploads/team/' . $imageNameToStore);
            $data['message'] = 'Member add Success';
            $data['r_url_con'] = true;
            $data['r_url'] = 'team_members.php';

        } else {
            $data['error'] = true;
            $data['message'] = 'Member add failed';
        }
    } else {

        $data['error'] = true;


        $name = $_POST['name'];
        $role = $_POST['role'];
        $short_desc = $_POST['short_desc'];

        if ($name == '') {
            $data['message'] = $team->error_message('title');
        } elseif (empty($_FILES['image']['name'])) {
            $data['message'] = 'Please select a image!';
        } elseif ($role == '') {
            $data['message'] = $team->error_message('status');
        } elseif ($short_desc == '') {
            $data['message'] = $team->error_message('description');
        } else {
            $data['message'] = 'Something Went Wrong!';
        }
    }

    echo json_encode($data);

}

if (isset($_POST['action']) && $_POST['action'] == 'team-member-status-change') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $status_update = $team->team_status_update($id, $status);

    if ($status_update) {
        $data['message'] = 'Status updated successfully!';
    } else {
        $data['error'] = 'true';
        $data['message'] = 'Status updated failed!';
    }
    echo json_encode($data);

}

if (isset($_POST['action']) && $_POST['action'] == 'team-member-delete') {

    $id = (int)($_POST['id']);
    $member = $team->team_member_find($id);

    if ($member->num_rows > 0) {

        $delete = $team->member_delete($id);

        if ($delete) {

            $member_row = $member->fetch_assoc();

            unlink('../../uploads/team/' . $member_row['image']);

            $data['message'] = 'Team member deleted successfully!';

        } else {
            $data['error'] = 'true';
            $data['message'] = 'Team member deleted failed!';
        }
    } else {
        $data['error'] = 'true';
        $data['message'] = 'Team member not found!';
    }


    echo json_encode($data);

}

if (isset($_POST['action']) && $_POST['action'] == 'update-team-member') {

    if (isset($_POST['name']) && isset($_POST['role']) && isset($_POST['short_desc'])) {

        $id = $_POST['data_id'];
        $id = base64_decode($id);
        $id = (int)$id;

        $member_array = $team->team_member_find($id);

        if ($member_array->num_rows > 0) {

            $member_row = $member_array->fetch_assoc();

            $name = $_POST['name'];
            $role = $_POST['role'];
            $short_desc = $_POST['short_desc'];
            $status = $_POST['status'];

            $facebook = $_POST['facebook'];
            $twitter = $_POST['twitter'];
            $instagram = $_POST['instagram'];
            $linkedIn = $_POST['linkedIn'];

            if ($_FILES['image']['name']) {
                $image = $_FILES['image'];

                $imageName = $image['name'];
                /** @var  $imageExe */
                $imageExe = explode('.', $imageName);
                $imageExe = end($imageExe);

                $imageNameToStore = uniqid() . rand(111111, 999999) . '.' . $imageExe;

            } else {
                $imageNameToStore = $member_row['image'];
            }

            $update = $team->update_member($name, $role, $short_desc, $status, $facebook, $twitter, $instagram, $linkedIn, $imageNameToStore, $id);
            if ($update) {
                if ($_FILES['image']['name']) {
                    unlink('../../uploads/team/' . $member_row['image']);
                    move_uploaded_file($image['tmp_name'], '../../uploads/team/' . $imageNameToStore);
                }
                $data['message'] = 'Member info save Success';
                $data['r_url_con'] = true;
                $data['r_url'] = 'team_members.php';

            } else {
                $data['error'] = true;
                $data['message'] = 'Member info save failed';
            }
        } else {
            $data['error'] = true;
            $data['message'] = 'Member Not found!';
        }
    } else {

        $data['error'] = true;

        $name = $_POST['name'];
        $role = $_POST['role'];
        $short_desc = $_POST['short_desc'];

        if ($name == '') {
            $data['message'] = $team->error_message('title');
        } elseif ($role == '') {
            $data['message'] = $team->error_message('role');
        } elseif ($short_desc == '') {
            $data['message'] = $team->error_message('description');
        } else {
            $data['message'] = 'Something Went Wrong!';
        }
    }
    echo json_encode($data);


}


if (isset($_POST['action']) && $_POST['action'] == 'add-service') {

    if (isset($_POST['title']) && isset($_POST['sub_title']) && isset($_POST['icon'])) {

        $title = $_POST['title'];
        $sub_title = $_POST['sub_title'];
        $icon = $_POST['icon'];
        $status = $_POST['status'];

        $result = $service->add_service($title, $sub_title, $icon, $status);

        if ($result) {

            $data['message'] = 'Service add Success';
            $data['r_url_con'] = true;
            $data['r_url'] = 'service.php';

        } else {
            $data['error'] = true;
            $data['message'] = 'Service add failed';
        }


    } else {

        $data['error'] = true;

        $title = $_POST['title'];
        $sub_title = $_POST['sub_title'];
        $icon = $_POST['icon'];

        if ($title == '') {
            $data['message'] = $team->error_message('title');
        } elseif ($sub_title == '') {
            $data['message'] = $team->error_message('sub title');
        } elseif ($icon == '') {
            $data['message'] = $team->error_message('icon');
        } else {
            $data['message'] = 'Something Went Wrong!';
        }
    }

    echo json_encode($data);

}

if (isset($_POST['action']) && $_POST['action'] == 'service-status-change') {

    $id = $_POST['id'];
    $status = $_POST['status'];

    $status_update = $service->service_status_update($id, $status);

    if ($status_update) {
        $data['message'] = 'Status updated successfully!';
    } else {
        $data['error'] = 'true';
        $data['message'] = 'Status updated failed!';
    }
    echo json_encode($data);

}

if (isset($_POST['action']) && $_POST['action'] == 'service-delete') {


    $id = (int)($_POST['id']);
    $service_ob = $service->service_find($id);

    if ($service_ob->num_rows > 0) {

        $delete = $service->service_delete($id);

        if ($delete) {
            $data['message'] = 'Service deleted successfully!';

        } else {
            $data['error'] = 'true';
            $data['message'] = 'Service deleted failed!';
        }
    } else {
        $data['error'] = 'true';
        $data['message'] = 'Service not found!';
    }


    echo json_encode($data);

}

if (isset($_POST['action']) && $_POST['action'] == 'edit-service') {


    if (isset($_POST['title']) && isset($_POST['sub_title']) && isset($_POST['icon']) && isset($_POST['data_id'])) {

        $id = $_POST['data_id'];
        $id = base64_decode($id);
        $id = (int)$id;

        $title = $_POST['title'];
        $sub_title = $_POST['sub_title'];
        $icon = $_POST['icon'];
        $status = $_POST['status'];

        $result = $service->update_service($title, $sub_title, $icon, $status, $id);

        if ($result) {

            $data['message'] = 'Service update Success';
            $data['r_url_con'] = true;
            $data['r_url'] = 'service.php';

        } else {
            $data['error'] = true;
            $data['message'] = 'Service update failed';
        }


    } else {

        $data['error'] = true;

        $title = $_POST['title'];
        $sub_title = $_POST['sub_title'];
        $icon = $_POST['icon'];
        $id = $_POST['data_id'];

        if ($title == '') {
            $data['message'] = $team->error_message('title');
        } elseif ($sub_title == '') {
            $data['message'] = $team->error_message('sub title');
        } elseif ($icon == '') {
            $data['message'] = $team->error_message('icon');
        } elseif ($id == '') {
            $data['message'] = $team->error_message('id');
        } else {
            $data['message'] = 'Something Went Wrong!';
        }
    }

    echo json_encode($data);

}


if (isset($_POST['action']) && $_POST['action'] == 'add-testimonial') {

    if (isset($_POST['name']) && isset($_POST['role']) && isset($_POST['review']) && $_FILES['image']['name']) {

        $name = $_POST['name'];
        $role = $_POST['role'];
        $review = $_POST['review'];
        $status = $_POST['status'];


//        images upload
        $image = $_FILES['image'];

        $imageName = $image['name'];
        /** @var  $imageExe */
        $imageExe = explode('.', $imageName);
        $imageExe = end($imageExe);

        $imageNameToStore = uniqid() . rand(111111, 999999) . '.' . $imageExe;

        $result = $testimonial->add_testimonial($name, $role, $review, $status, $imageNameToStore);
        if ($result) {
            move_uploaded_file($image['tmp_name'], '../../uploads/testimonials/' . $imageNameToStore);
            $data['message'] = 'Testimonial add Success';
            $data['r_url_con'] = true;
            $data['r_url'] = 'testimonials.php';

        } else {
            $data['error'] = true;
            $data['message'] = 'Testimonial add failed';
        }
    } else {

        $data['error'] = true;


        $name = $_POST['name'];
        $role = $_POST['role'];
        $review = $_POST['review'];

        if ($name == '') {
            $data['message'] = $team->error_message('title');
        } elseif (empty($_FILES['image']['name'])) {
            $data['message'] = 'Please select a image!';
        } elseif ($role == '') {
            $data['message'] = $team->error_message('role');
        } elseif ($review == '') {
            $data['message'] = $team->error_message('review');
        } else {
            $data['message'] = 'Something Went Wrong!';
        }
    }

    echo json_encode($data);

}

if (isset($_POST['action']) && $_POST['action'] == 'testimonials-status-change') {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $status_update = $testimonial->testimonials_status_update($id, $status);

    if ($status_update) {
        $data['message'] = 'Status updated successfully!';
    } else {
        $data['error'] = 'true';
        $data['message'] = 'Status updated failed!';
    }
    echo json_encode($data);

}

if (isset($_POST['action']) && $_POST['action'] == 'testimonial-delete') {

    $id = (int)($_POST['id']);

    $testimonial_array = $testimonial->testimonial_find($id);

    if ($testimonial_array->num_rows > 0) {

        $delete = $testimonial->testimonial_delete($id);

        if ($delete) {

            $testimonial_row = $testimonial_array->fetch_assoc();

            unlink('../../uploads/testimonials/' . $testimonial_row['image']);

            $data['message'] = 'Testimonial deleted successfully!';

        } else {
            $data['error'] = 'true';
            $data['message'] = 'Testimonial deleted failed!';
        }
    } else {
        $data['error'] = 'true';
        $data['message'] = 'Testimonial not found!';
    }


    echo json_encode($data);

}

if (isset($_POST['action']) && $_POST['action'] == 'edit-testimonial') {

    if (isset($_POST['name']) && isset($_POST['role']) && isset($_POST['review'])) {

        $name = $_POST['name'];
        $role = $_POST['role'];
        $review = $_POST['review'];
        $status = $_POST['status'];

        $id = base64_decode($_POST['data_id']);
        $id = (int)$id;


        $testimonial_array = $testimonial->testimonial_find($id);

        if ($testimonial_array->num_rows > 0) {

            $testimonial_row = $testimonial_array->fetch_assoc();

            if ($_FILES['image']['name']) {
                $image = $_FILES['image'];

                $imageName = $image['name'];
                /** @var  $imageExe */
                $imageExe = explode('.', $imageName);
                $imageExe = end($imageExe);

                $imageNameToStore = uniqid() . rand(111111, 999999) . '.' . $imageExe;

            } else {
                $imageNameToStore = $testimonial_row['image'];
            }

//        images upload

            $result = $testimonial->edit_testimonial($name, $role, $review, $status, $imageNameToStore, $id);
            if ($result) {
                if ($_FILES['image']['name']) {

                    unlink('../../uploads/testimonials/' . $testimonial_row['image']);
                    move_uploaded_file($image['tmp_name'], '../../uploads/testimonials/' . $imageNameToStore);
                }

                $data['message'] = 'Testimonial add Success';
                $data['r_url_con'] = true;
                $data['r_url'] = 'testimonials.php';

            } else {
                $data['error'] = true;
                $data['message'] = 'Testimonial add failed';
            }
        } else {

            $data['error'] = true;


            $name = $_POST['name'];
            $role = $_POST['role'];
            $review = $_POST['review'];

            if ($name == '') {
                $data['message'] = $team->error_message('title');
            } elseif (empty($_FILES['image']['name'])) {
                $data['message'] = 'Please select a image!';
            } elseif ($role == '') {
                $data['message'] = $team->error_message('role');
            } elseif ($review == '') {
                $data['message'] = $team->error_message('review');
            } else {
                $data['message'] = 'Something Went Wrong!';
            }
        }
    }

    echo json_encode($data);

}


if (isset($_POST['action']) && $_POST['action'] == 'add-skill') {

    if (isset($_POST['name']) && $_POST['name'] != '' && isset($_POST['percentage']) && $_POST['percentage'] != '' && isset($_POST['status'])) {

        $name = $_POST['name'];
        $percentage = $_POST['percentage'];
        $status = $_POST['status'];

        $add_skill = $skills->add_skill($name, $percentage, $status);

        if ($add_skill) {
            $data['message'] = 'skill added successfully!';
            $data['r_url_con'] = true;
            $data['r_url'] = 'skills.php';
        } else {
            $data['error'] = 'true';
            $data['message'] = 'skill added failed!';
        }
    } else {

        $data['error'] = true;

        $name = $_POST['name'];
        $percentage = $_POST['percentage'];
        $status = $_POST['status'];

        if ($name == '') {
            $data['message'] = $team->error_message('name');
        } elseif ($percentage == '') {
            $data['message'] = $team->error_message('percentage');
        } elseif ($status == '') {
            $data['message'] = $team->error_message('status');
        } else {
            $data['message'] = 'Something Went Wrong!';
        }
    }


    echo json_encode($data);

}
// work menu status update
if (isset($_POST['action']) && $_POST['action'] == 'skill-status') {

    $id = $_POST['id'];
    $status = $_POST['status'];

    $status_update = $skills->skill_status_change($id, $status);

    if ($status_update) {
        $data['message'] = 'Status updated successfully!';
    } else {
        $data['error'] = 'true';
        $data['message'] = 'Status updated failed!';
    }
    echo json_encode($data);

}
// work menu update
if (isset($_POST['action']) && $_POST['action'] == 'update-skill') {


    if (isset($_POST['name']) && $_POST['name'] != '' && isset($_POST['percentage']) && $_POST['percentage'] != '' && isset($_POST['status'])) {

        $name = $_POST['name'];
        $percentage = $_POST['percentage'];
        $status = $_POST['status'];
        $id = base64_decode($_POST['data_id']);
        $id = (int)$id;

        $update_skill = $skills->update_skill($name, $percentage, $status, $id);

        if ($update_skill) {
            $data['message'] = 'Skill updated successfully!';
            $data['r_url_con'] = true;
            $data['r_url'] = 'skills.php';
        } else {
            $data['error'] = 'true';
            $data['message'] = 'Skill update failed!';
        }
    } else {

        $data['error'] = true;

        $name = $_POST['name'];
        $percentage = $_POST['percentage'];
        $status = $_POST['status'];

        if ($name == '') {
            $data['message'] = $team->error_message('name');
        } elseif ($percentage == '') {
            $data['message'] = $team->error_message('percentage');
        } elseif ($status == '') {
            $data['message'] = $team->error_message('status');
        } else {
            $data['message'] = 'Something Went Wrong!';
        }
    }


    echo json_encode($data);
}

if (isset($_POST['action']) && $_POST['action'] == 'skill-delete') {

    $id = (int)$_POST['id'];

    $delete = $skills->skill_delete($id);

    if ($delete) {
        $data['message'] = 'Skill deleted successfully!';

    } else {
        $data['error'] = 'true';
        $data['message'] = 'Skill deleted failed!';
    }

    echo json_encode($data);

}


if (isset($_POST['action']) && $_POST['action'] == 'option_text_update' || $_POST['action'] == 'option_data_status') {

    if ($_POST['action'] == 'option_data_status') {
        $value = $_POST['status'];
    } else if ($_POST['action'] == 'option_text_update') {
        $value = $_POST['value'];
    }

    $id = $_POST['id'];

    $save = $options->option_update($value, $id);
    if ($save) {
        $data['message'] = 'Data Updated successfully!';
    } else {
        $data['error'] = 'true';
        $data['message'] = 'Data Updated failed!';
    }
    echo json_encode($data);
}


if (isset($_POST['action']) && $_POST['action'] == 'contact-message-delete') {

    $id = (int)$_POST['id'];

    $delete = $adminExtras->message_delete($id);

    if ($delete) {
        $data['message'] = 'Message deleted successfully!';

    } else {
        $data['error'] = 'true';
        $data['message'] = 'Message deleted failed!';
    }

    echo json_encode($data);

}


if (isset($_POST['action']) && $_POST['action'] == 'profile-info') {

    if (isset($_POST['name']) && isset($_POST['email'])) {

        $name = $_POST['name'];
        $email = $_POST['email'];

        $update = $auth->update_profile_info($name, $email);
        if ($update) {
            $data['message'] = 'Profile Info updated successfully!';

        } else {
            $data['error'] = 'true';
            $data['message'] = 'Profile Info updated  failed!';
        }
    } else {

        $data['error'] = true;

        $name = $_POST['name'];
        $email = $_POST['email'];

        if ($name == '') {
            $data['message'] = $team->error_message('name');
        } elseif ($email == '') {
            $data['message'] = $team->error_message('email');
        } else {
            $data['message'] = 'Something Went Wrong!';
        }
    }
    echo json_encode($data);

}


if (isset($_POST['action']) && $_POST['action'] == 'password-change') {


    if (isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {

        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password === $confirm_password) {
            $user = $auth->get_auth_user();
            $password_hash = $user['password'];

            if (password_verify($current_password, $password_hash)) {

                $new_password = password_hash($new_password, PASSWORD_DEFAULT);

                $update_password = $auth->update_password($new_password);
                if ($update_password) {
                    $data['message'] = 'Password updated successfully!';
                } else {
                    $data['error'] = 'true';
                    $data['message'] = 'Password updated  failed!';
                }


            } else {
                $data['error'] = true;
                $data['message'] = "You entered a wrong password!!";
            }


        } else {
            $data['error'] = true;
            $data['message'] = "Password didn't matched!";
        }
    } else {

        $data['error'] = true;

        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($current_password == '') {
            $data['message'] = $auth->error_message('current password');
        } elseif ($new_password == '') {
            $data['message'] = $auth->error_message('new password');
        } elseif ($confirm_password == '') {
            $data['message'] = $auth->error_message('confirm password');
        } else {
            $data['message'] = 'Something Went Wrong!';
        }
    }
    echo json_encode($data);

}


if (isset($_POST['action']) && $_POST['action'] == base64_encode('update-profile-image')) {

//        images upload
    $image = $_FILES['profile_image'];

    $imageName = $image['name'];
    /** @var  $imageExe */
    $imageExe = explode('.', $imageName);
    $imageExe = end($imageExe);

    $imageNameToStore = uniqid() . rand(111111, 999999) . '.' . $imageExe;

    $email = base64_decode($_POST['user_email']);
    $user = $auth->get_user($email);
    $user = $user->fetch_assoc();

    $update = $auth->user_photo_update($imageNameToStore, $user['id']);
    if ($update) {

        if ($user['photo']) {
            unlink('../../uploads/users/' . $user['photo']);
        }
        move_uploaded_file($image['tmp_name'], '../../uploads/users/' . $imageNameToStore);
    }

    header('location:location:javascript://history.go(-1)');

}

if (isset($_POST['action']) && $_POST['action'] == 'client-logo-add') {

    if (!empty($_FILES['image']['name'])) {

        $status = $_POST['status'];

//        images upload
        $image = $_FILES['image'];

        $imageName = $image['name'];
        /** @var  $imageExe */
        $imageExe = explode('.', $imageName);
        $imageExe = end($imageExe);

        $imageNameToStore = uniqid() . rand(111111, 999999) . '.' . $imageExe;
        $add_logo = $client->add_client_logo($imageNameToStore, $status);
        if ($add_logo) {
            $data['r_url_con'] = true;
            $data['r_url'] = 'client_logo.php';
            move_uploaded_file($image['tmp_name'], '../../uploads/clients/' . $imageNameToStore);

            $data['message'] = 'Client Logo Added Successfully!';
        } else {
            $data['error'] = true;
            $data['message'] = 'Please select a image!';
        }

    } else {

        $data['error'] = true;

        if (!$_FILES['image']['name']) {
            $data['message'] = 'Please select a image!';
        } else {
            $data['message'] = 'Something Went Wrong!';
        }
    }
    echo json_encode($data);
}
if (isset($_POST['action']) && $_POST['action'] == 'client-logo-status-change'){

    $id = $_POST['id'];
    $status = $_POST['status'];

    $status_update = $client->client_logo_status($status , $id);

    if ($status_update) {
        $data['message'] = 'Status updated successfully!';
    } else {
        $data['error'] = 'true';
        $data['message'] = 'Status updated failed!';
    }
    echo json_encode($data);

}

if (isset($_POST['action']) && $_POST['action'] == 'client-logo-delete') {

    $id = (int)$_POST['id'];

    $logo = $client->client_logo_find($id);

    $delete = $client->logo_delete($id);

    if ($delete) {

        unlink('../../uploads/clients/' . $logo['image']);
        $data['message'] = 'Client Logo deleted successfully!';

    } else {
        $data['error'] = 'true';
        $data['message'] = 'Client Logo deleted failed!';
    }

    echo json_encode($data);

}
