<?php

$login = 'labsales_test';
$password = '18765gR5';

function getData($url, $login, $password, $urlExtension = '') {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url . $urlExtension);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "$login:$password");
    $data = curl_exec($curl);
    curl_close($curl);

    return json_decode($data, true);
}


//Получить список категории
$data = getData(
    'https://test.labsales.ru/tasks/articles/rest/categories',
    $login,
    $password
);

if (empty($data['error'])) {
    $categoriesList = $data['data'];
} else {
    die($data['error']);
}

//Получить список статей в категории
foreach ($categoriesList as $category) {
    $data = getData(
        'https://test.labsales.ru/tasks/articles/rest/category/',
        $login,
        $password,
        $category['category_id']
    );

    if (empty($data['error'])) {
        $postsList[$category['name']] = $data['data'];
    } else {
        die($data['error']);
    }
}


//Получить посты
foreach ($postsList as $categoryName => $categoryPosts) {
    foreach ($categoryPosts as $post) {
        $data = getData(
            'https://test.labsales.ru/tasks/articles/rest/article/',
            $login,
            $password,
            $post['article_id']
        );

        if (empty($data['error'])) {
            $posts[$categoryName][] = $data['data'];
        } else {
            die($data['error']);
        }
    }
} ?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <title>Test</title>
</head>
<body>
    <div class="wrapper mb-5 mt-5">
        <div class="container">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <?php $i = 0; foreach ($categoriesList as $category): $i++; ?>
                        <a class="nav-item nav-link<?= $i === 1 ? ' active' : ''; ?>" id="nav-<?= $i; ?>-tab" data-toggle="tab" href="#nav-<?= $i; ?>" role="tab" aria-controls="nav-<?= $i; ?>" aria-selected="true"><?= $category['name'] ?></a>
                    <?php endforeach; ?>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <?php $i = 0; foreach ($posts as $postInCategory): $i++; ?>
                    <div class="tab-pane fade show <?= $i === 1 ? ' active' : ''; ?>" id="nav-<?= $i; ?>" role="tabpanel">
                        <div class="row mb-4">
                            <?php foreach ($postInCategory as $post): ?>
                                <div class="col-12 mt-3 mb-3">
                                    <h2><?= $post['name'] ?></h2>
                                    <p><i><?= $post['date'] ?></i></p>
                                    <p><?= $post['text'] ?></p>
                                </div>
                                <div class="col-12"><hr></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>