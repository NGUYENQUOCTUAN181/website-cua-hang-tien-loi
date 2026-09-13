<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$messages = [];
$error = '';

try {
    $pdo->beginTransaction();

    $categoryIds = [];
    $findCategory = $pdo->prepare("SELECT id FROM categories WHERE name = :name LIMIT 1");
    $insertCategory = $pdo->prepare("INSERT INTO categories (name, status) VALUES (:name, 'active')");

    $findCategory->execute(['name' => 'Đồ uống']);
    $categoryIds['Đồ uống'] = $findCategory->fetchColumn();
    if ($categoryIds['Đồ uống'] === false) {
        $insertCategory->execute(['name' => 'Đồ uống']);
        $categoryIds['Đồ uống'] = $pdo->lastInsertId();
    }

    $findCategory->execute(['name' => 'Mì & đồ ăn liền']);
    $categoryIds['Mì & đồ ăn liền'] = $findCategory->fetchColumn();
    if ($categoryIds['Mì & đồ ăn liền'] === false) {
        $insertCategory->execute(['name' => 'Mì & đồ ăn liền']);
        $categoryIds['Mì & đồ ăn liền'] = $pdo->lastInsertId();
    }

    $findCategory->execute(['name' => 'Bánh & kẹo']);
    $categoryIds['Bánh & kẹo'] = $findCategory->fetchColumn();
    if ($categoryIds['Bánh & kẹo'] === false) {
        $insertCategory->execute(['name' => 'Bánh & kẹo']);
        $categoryIds['Bánh & kẹo'] = $pdo->lastInsertId();
    }

    $findCategory->execute(['name' => 'Snack']);
    $categoryIds['Snack'] = $findCategory->fetchColumn();
    if ($categoryIds['Snack'] === false) {
        $insertCategory->execute(['name' => 'Snack']);
        $categoryIds['Snack'] = $pdo->lastInsertId();
    }

    $findCategory->execute(['name' => 'Sữa & dinh dưỡng']);
    $categoryIds['Sữa & dinh dưỡng'] = $findCategory->fetchColumn();
    if ($categoryIds['Sữa & dinh dưỡng'] === false) {
        $insertCategory->execute(['name' => 'Sữa & dinh dưỡng']);
        $categoryIds['Sữa & dinh dưỡng'] = $pdo->lastInsertId();
    }

    $findCategory->execute(['name' => 'Thực phẩm khô']);
    $categoryIds['Thực phẩm khô'] = $findCategory->fetchColumn();
    if ($categoryIds['Thực phẩm khô'] === false) {
        $insertCategory->execute(['name' => 'Thực phẩm khô']);
        $categoryIds['Thực phẩm khô'] = $pdo->lastInsertId();
    }

    $findCategory->execute(['name' => 'Chăm sóc cá nhân']);
    $categoryIds['Chăm sóc cá nhân'] = $findCategory->fetchColumn();
    if ($categoryIds['Chăm sóc cá nhân'] === false) {
        $insertCategory->execute(['name' => 'Chăm sóc cá nhân']);
        $categoryIds['Chăm sóc cá nhân'] = $pdo->lastInsertId();
    }

    $findCategory->execute(['name' => 'Gia dụng']);
    $categoryIds['Gia dụng'] = $findCategory->fetchColumn();
    if ($categoryIds['Gia dụng'] === false) {
        $insertCategory->execute(['name' => 'Gia dụng']);
        $categoryIds['Gia dụng'] = $pdo->lastInsertId();
    }

    $findCategory->execute(['name' => 'Đồ ăn nhanh']);
    $categoryIds['Đồ ăn nhanh'] = $findCategory->fetchColumn();
    if ($categoryIds['Đồ ăn nhanh'] === false) {
        $insertCategory->execute(['name' => 'Đồ ăn nhanh']);
        $categoryIds['Đồ ăn nhanh'] = $pdo->lastInsertId();
    }

    $brandIds = [];
    $findBrand = $pdo->prepare("SELECT id FROM brands WHERE name = :name LIMIT 1");
    $insertBrand = $pdo->prepare("INSERT INTO brands (name, status) VALUES (:name, 'active')");
    $findBrand->execute(['name' => '3 Miền']);
    $brandIds['3 Miền'] = $findBrand->fetchColumn();
    if ($brandIds['3 Miền'] === false) {
        $insertBrand->execute(['name' => '3 Miền']);
        $brandIds['3 Miền'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => '7Up']);
    $brandIds['7Up'] = $findBrand->fetchColumn();
    if ($brandIds['7Up'] === false) {
        $insertBrand->execute(['name' => '7Up']);
        $brandIds['7Up'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Alpenliebe']);
    $brandIds['Alpenliebe'] = $findBrand->fetchColumn();
    if ($brandIds['Alpenliebe'] === false) {
        $insertBrand->execute(['name' => 'Alpenliebe']);
        $brandIds['Alpenliebe'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'C2']);
    $brandIds['C2'] = $findBrand->fetchColumn();
    if ($brandIds['C2'] === false) {
        $insertBrand->execute(['name' => 'C2']);
        $brandIds['C2'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'CP']);
    $brandIds['CP'] = $findBrand->fetchColumn();
    if ($brandIds['CP'] === false) {
        $insertBrand->execute(['name' => 'CP']);
        $brandIds['CP'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Chinsu']);
    $brandIds['Chinsu'] = $findBrand->fetchColumn();
    if ($brandIds['Chinsu'] === false) {
        $insertBrand->execute(['name' => 'Chinsu']);
        $brandIds['Chinsu'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'ChocoPie']);
    $brandIds['ChocoPie'] = $findBrand->fetchColumn();
    if ($brandIds['ChocoPie'] === false) {
        $insertBrand->execute(['name' => 'ChocoPie']);
        $brandIds['ChocoPie'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Coca-Cola']);
    $brandIds['Coca-Cola'] = $findBrand->fetchColumn();
    if ($brandIds['Coca-Cola'] === false) {
        $insertBrand->execute(['name' => 'Coca-Cola']);
        $brandIds['Coca-Cola'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Colgate']);
    $brandIds['Colgate'] = $findBrand->fetchColumn();
    if ($brandIds['Colgate'] === false) {
        $insertBrand->execute(['name' => 'Colgate']);
        $brandIds['Colgate'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Comfort']);
    $brandIds['Comfort'] = $findBrand->fetchColumn();
    if ($brandIds['Comfort'] === false) {
        $insertBrand->execute(['name' => 'Comfort']);
        $brandIds['Comfort'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Cosy']);
    $brandIds['Cosy'] = $findBrand->fetchColumn();
    if ($brandIds['Cosy'] === false) {
        $insertBrand->execute(['name' => 'Cosy']);
        $brandIds['Cosy'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Dove']);
    $brandIds['Dove'] = $findBrand->fetchColumn();
    if ($brandIds['Dove'] === false) {
        $insertBrand->execute(['name' => 'Dove']);
        $brandIds['Dove'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Dutch Lady']);
    $brandIds['Dutch Lady'] = $findBrand->fetchColumn();
    if ($brandIds['Dutch Lady'] === false) {
        $insertBrand->execute(['name' => 'Dutch Lady']);
        $brandIds['Dutch Lady'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Gift']);
    $brandIds['Gift'] = $findBrand->fetchColumn();
    if ($brandIds['Gift'] === false) {
        $insertBrand->execute(['name' => 'Gift']);
        $brandIds['Gift'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Head & Shoulders']);
    $brandIds['Head & Shoulders'] = $findBrand->fetchColumn();
    if ($brandIds['Head & Shoulders'] === false) {
        $insertBrand->execute(['name' => 'Head & Shoulders']);
        $brandIds['Head & Shoulders'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Hảo Hảo']);
    $brandIds['Hảo Hảo'] = $findBrand->fetchColumn();
    if ($brandIds['Hảo Hảo'] === false) {
        $insertBrand->execute(['name' => 'Hảo Hảo']);
        $brandIds['Hảo Hảo'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'KitKat']);
    $brandIds['KitKat'] = $findBrand->fetchColumn();
    if ($brandIds['KitKat'] === false) {
        $insertBrand->execute(['name' => 'KitKat']);
        $brandIds['KitKat'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Knorr']);
    $brandIds['Knorr'] = $findBrand->fetchColumn();
    if ($brandIds['Knorr'] === false) {
        $insertBrand->execute(['name' => 'Knorr']);
        $brandIds['Knorr'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Kokomi']);
    $brandIds['Kokomi'] = $findBrand->fetchColumn();
    if ($brandIds['Kokomi'] === false) {
        $insertBrand->execute(['name' => 'Kokomi']);
        $brandIds['Kokomi'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => "Lay's"]);
    $brandIds["Lay's"] = $findBrand->fetchColumn();
    if ($brandIds["Lay's"] === false) {
        $insertBrand->execute(['name' => "Lay's"]);
        $brandIds["Lay's"] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Lifebuoy']);
    $brandIds['Lifebuoy'] = $findBrand->fetchColumn();
    if ($brandIds['Lifebuoy'] === false) {
        $insertBrand->execute(['name' => 'Lifebuoy']);
        $brandIds['Lifebuoy'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Mentos']);
    $brandIds['Mentos'] = $findBrand->fetchColumn();
    if ($brandIds['Mentos'] === false) {
        $insertBrand->execute(['name' => 'Mentos']);
        $brandIds['Mentos'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Milo']);
    $brandIds['Milo'] = $findBrand->fetchColumn();
    if ($brandIds['Milo'] === false) {
        $insertBrand->execute(['name' => 'Milo']);
        $brandIds['Milo'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Mirinda']);
    $brandIds['Mirinda'] = $findBrand->fetchColumn();
    if ($brandIds['Mirinda'] === false) {
        $insertBrand->execute(['name' => 'Mirinda']);
        $brandIds['Mirinda'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Monster']);
    $brandIds['Monster'] = $findBrand->fetchColumn();
    if ($brandIds['Monster'] === false) {
        $insertBrand->execute(['name' => 'Monster']);
        $brandIds['Monster'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Nam Ngư']);
    $brandIds['Nam Ngư'] = $findBrand->fetchColumn();
    if ($brandIds['Nam Ngư'] === false) {
        $insertBrand->execute(['name' => 'Nam Ngư']);
        $brandIds['Nam Ngư'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Number 1']);
    $brandIds['Number 1'] = $findBrand->fetchColumn();
    if ($brandIds['Number 1'] === false) {
        $insertBrand->execute(['name' => 'Number 1']);
        $brandIds['Number 1'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'OMO']);
    $brandIds['OMO'] = $findBrand->fetchColumn();
    if ($brandIds['OMO'] === false) {
        $insertBrand->execute(['name' => 'OMO']);
        $brandIds['OMO'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Oishi']);
    $brandIds['Oishi'] = $findBrand->fetchColumn();
    if ($brandIds['Oishi'] === false) {
        $insertBrand->execute(['name' => 'Oishi']);
        $brandIds['Oishi'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Omachi']);
    $brandIds['Omachi'] = $findBrand->fetchColumn();
    if ($brandIds['Omachi'] === false) {
        $insertBrand->execute(['name' => 'Omachi']);
        $brandIds['Omachi'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Oreo']);
    $brandIds['Oreo'] = $findBrand->fetchColumn();
    if ($brandIds['Oreo'] === false) {
        $insertBrand->execute(['name' => 'Oreo']);
        $brandIds['Oreo'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'P/S']);
    $brandIds['P/S'] = $findBrand->fetchColumn();
    if ($brandIds['P/S'] === false) {
        $insertBrand->execute(['name' => 'P/S']);
        $brandIds['P/S'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Pepsi']);
    $brandIds['Pepsi'] = $findBrand->fetchColumn();
    if ($brandIds['Pepsi'] === false) {
        $insertBrand->execute(['name' => 'Pepsi']);
        $brandIds['Pepsi'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Phở Đệ Nhất']);
    $brandIds['Phở Đệ Nhất'] = $findBrand->fetchColumn();
    if ($brandIds['Phở Đệ Nhất'] === false) {
        $insertBrand->execute(['name' => 'Phở Đệ Nhất']);
        $brandIds['Phở Đệ Nhất'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Poca']);
    $brandIds['Poca'] = $findBrand->fetchColumn();
    if ($brandIds['Poca'] === false) {
        $insertBrand->execute(['name' => 'Poca']);
        $brandIds['Poca'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Pocky']);
    $brandIds['Pocky'] = $findBrand->fetchColumn();
    if ($brandIds['Pocky'] === false) {
        $insertBrand->execute(['name' => 'Pocky']);
        $brandIds['Pocky'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Red Bull']);
    $brandIds['Red Bull'] = $findBrand->fetchColumn();
    if ($brandIds['Red Bull'] === false) {
        $insertBrand->execute(['name' => 'Red Bull']);
        $brandIds['Red Bull'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Revive']);
    $brandIds['Revive'] = $findBrand->fetchColumn();
    if ($brandIds['Revive'] === false) {
        $insertBrand->execute(['name' => 'Revive']);
        $brandIds['Revive'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Sting']);
    $brandIds['Sting'] = $findBrand->fetchColumn();
    if ($brandIds['Sting'] === false) {
        $insertBrand->execute(['name' => 'Sting']);
        $brandIds['Sting'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Sunlight']);
    $brandIds['Sunlight'] = $findBrand->fetchColumn();
    if ($brandIds['Sunlight'] === false) {
        $insertBrand->execute(['name' => 'Sunlight']);
        $brandIds['Sunlight'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Sunsilk']);
    $brandIds['Sunsilk'] = $findBrand->fetchColumn();
    if ($brandIds['Sunsilk'] === false) {
        $insertBrand->execute(['name' => 'Sunsilk']);
        $brandIds['Sunsilk'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'TH True Milk']);
    $brandIds['TH True Milk'] = $findBrand->fetchColumn();
    if ($brandIds['TH True Milk'] === false) {
        $insertBrand->execute(['name' => 'TH True Milk']);
        $brandIds['TH True Milk'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Vinamilk']);
    $brandIds['Vinamilk'] = $findBrand->fetchColumn();
    if ($brandIds['Vinamilk'] === false) {
        $insertBrand->execute(['name' => 'Vinamilk']);
        $brandIds['Vinamilk'] = $pdo->lastInsertId();
    }
    $findBrand->execute(['name' => 'Vissan']);
    $brandIds['Vissan'] = $findBrand->fetchColumn();
    if ($brandIds['Vissan'] === false) {
        $insertBrand->execute(['name' => 'Vissan']);
        $brandIds['Vissan'] = $pdo->lastInsertId();
    }

    $columns = [];
    foreach ($pdo->query("SHOW COLUMNS FROM products")->fetchAll() as $col) {
        $columns[$col['Field']] = true;
    }

    foreach (['name','category_id','brand_id','price','stock','status'] as $required) {
        if (!isset($columns[$required])) {
            throw new RuntimeException("Thiếu cột products.$required");
        }
    }

    $findSlug = isset($columns['slug'])
        ? $pdo->prepare("SELECT id FROM products WHERE slug = :slug LIMIT 1")
        : null;

    $findName = $pdo->prepare("SELECT id FROM products WHERE name = :name LIMIT 1");
    $inserted = 0;
    $skipped = 0;

    if ($findSlug) {
        $findSlug->execute(['slug' => 'pepsi-black-330ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Pepsi Black 330ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Pepsi Black 330ml',
            'category_id' => $categoryIds['Đồ uống'],
            'brand_id' => $brandIds['Pepsi'],
            'price' => 10000,
            'sale_price' => 9000,
            'stock' => 72,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'pepsi-black-330ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Pepsi cola không đường lon 330ml.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'coca-cola-zero-330ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Coca-Cola Zero 330ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Coca-Cola Zero 330ml',
            'category_id' => $categoryIds['Đồ uống'],
            'brand_id' => $brandIds['Coca-Cola'],
            'price' => 10000,
            'sale_price' => 9000,
            'stock' => 65,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'coca-cola-zero-330ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Coca-Cola Zero lon 330ml.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'sting-vang-330ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Sting Vàng 330ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Sting Vàng 330ml',
            'category_id' => $categoryIds['Đồ uống'],
            'brand_id' => $brandIds['Sting'],
            'price' => 10000,
            'sale_price' => 9000,
            'stock' => 54,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'sting-vang-330ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Nước tăng lực Sting vị vàng.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'red-bull-250ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Red Bull 250ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Red Bull 250ml',
            'category_id' => $categoryIds['Đồ uống'],
            'brand_id' => $brandIds['Red Bull'],
            'price' => 15000,
            'sale_price' => 13500,
            'stock' => 48,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'red-bull-250ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Nước tăng lực Red Bull.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'monster-energy-355ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Monster Energy 355ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Monster Energy 355ml',
            'category_id' => $categoryIds['Đồ uống'],
            'brand_id' => $brandIds['Monster'],
            'price' => 35000,
            'sale_price' => 32000,
            'stock' => 28,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'monster-energy-355ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Nước tăng lực Monster Energy.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => '7up-330ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => '7Up Lemon Lime 330ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => '7Up Lemon Lime 330ml',
            'category_id' => $categoryIds['Đồ uống'],
            'brand_id' => $brandIds['7Up'],
            'price' => 9000,
            'sale_price' => 8000,
            'stock' => 60,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = '7up-330ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Nước ngọt 7Up.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'mirinda-cam-330ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Mirinda Cam 330ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Mirinda Cam 330ml',
            'category_id' => $categoryIds['Đồ uống'],
            'brand_id' => $brandIds['Mirinda'],
            'price' => 9000,
            'sale_price' => 8000,
            'stock' => 58,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'mirinda-cam-330ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Nước ngọt Mirinda cam.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'c2-tra-xanh-455ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'C2 Trà Xanh 455ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'C2 Trà Xanh 455ml',
            'category_id' => $categoryIds['Đồ uống'],
            'brand_id' => $brandIds['C2'],
            'price' => 10000,
            'sale_price' => 8500,
            'stock' => 73,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'c2-tra-xanh-455ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Trà xanh C2.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'revive-chanh-muoi-390ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Revive Chanh Muối 390ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Revive Chanh Muối 390ml',
            'category_id' => $categoryIds['Đồ uống'],
            'brand_id' => $brandIds['Revive'],
            'price' => 10000,
            'sale_price' => 9000,
            'stock' => 55,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'revive-chanh-muoi-390ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Nước uống thể thao Revive.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'number-1-330ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Number 1 330ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Number 1 330ml',
            'category_id' => $categoryIds['Đồ uống'],
            'brand_id' => $brandIds['Number 1'],
            'price' => 10000,
            'sale_price' => 9000,
            'stock' => 49,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'number-1-330ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Nước tăng lực Number 1.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'mi-hao-hao-tom-sa-te-75g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Mì Hảo Hảo Tôm Sa Tế 75g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Mì Hảo Hảo Tôm Sa Tế 75g',
            'category_id' => $categoryIds['Mì & đồ ăn liền'],
            'brand_id' => $brandIds['Hảo Hảo'],
            'price' => 5000,
            'sale_price' => 4500,
            'stock' => 180,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'mi-hao-hao-tom-sa-te-75g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Mì ăn liền Hảo Hảo vị tôm sa tế.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'mi-3-mien-tom-chua-cay-65g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Mì 3 Miền Tôm Chua Cay 65g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Mì 3 Miền Tôm Chua Cay 65g',
            'category_id' => $categoryIds['Mì & đồ ăn liền'],
            'brand_id' => $brandIds['3 Miền'],
            'price' => 4500,
            'sale_price' => 4000,
            'stock' => 165,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'mi-3-mien-tom-chua-cay-65g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Mì 3 Miền tôm chua cay.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'mi-3-mien-bo-ham-65g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Mì 3 Miền Bò Hầm 65g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Mì 3 Miền Bò Hầm 65g',
            'category_id' => $categoryIds['Mì & đồ ăn liền'],
            'brand_id' => $brandIds['3 Miền'],
            'price' => 4500,
            'sale_price' => 4000,
            'stock' => 150,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'mi-3-mien-bo-ham-65g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Mì 3 Miền bò hầm.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'mi-kokomi-tom-chua-cay-65g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Mì Kokomi Tôm Chua Cay 65g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Mì Kokomi Tôm Chua Cay 65g',
            'category_id' => $categoryIds['Mì & đồ ăn liền'],
            'brand_id' => $brandIds['Kokomi'],
            'price' => 4000,
            'sale_price' => 3500,
            'stock' => 210,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'mi-kokomi-tom-chua-cay-65g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Mì Kokomi tôm chua cay.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'mi-omachi-spaghetti-sot-ca-chua']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Mì Omachi Spaghetti Sốt Cà Chua']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Mì Omachi Spaghetti Sốt Cà Chua',
            'category_id' => $categoryIds['Mì & đồ ăn liền'],
            'brand_id' => $brandIds['Omachi'],
            'price' => 12000,
            'sale_price' => 10500,
            'stock' => 82,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'mi-omachi-spaghetti-sot-ca-chua';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Mì Omachi spaghetti.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'mi-omachi-tom-hum']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Mì Omachi Khoai Tây Tôm Hùm']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Mì Omachi Khoai Tây Tôm Hùm',
            'category_id' => $categoryIds['Mì & đồ ăn liền'],
            'brand_id' => $brandIds['Omachi'],
            'price' => 15000,
            'sale_price' => 13500,
            'stock' => 60,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'mi-omachi-tom-hum';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Mì Omachi vị tôm hùm.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'pho-de-nhat-bo-ham']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Phở Đệ Nhất Bò Hầm']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Phở Đệ Nhất Bò Hầm',
            'category_id' => $categoryIds['Mì & đồ ăn liền'],
            'brand_id' => $brandIds['Phở Đệ Nhất'],
            'price' => 9000,
            'sale_price' => 8000,
            'stock' => 95,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'pho-de-nhat-bo-ham';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Phở ăn liền bò hầm.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'pho-de-nhat-ga']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Phở Đệ Nhất Gà']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Phở Đệ Nhất Gà',
            'category_id' => $categoryIds['Mì & đồ ăn liền'],
            'brand_id' => $brandIds['Phở Đệ Nhất'],
            'price' => 9000,
            'sale_price' => 8000,
            'stock' => 90,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'pho-de-nhat-ga';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Phở ăn liền vị gà.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'banh-cosy-marie-300g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Bánh Cosy Marie 300g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Bánh Cosy Marie 300g',
            'category_id' => $categoryIds['Bánh & kẹo'],
            'brand_id' => $brandIds['Cosy'],
            'price' => 32000,
            'sale_price' => 29000,
            'stock' => 40,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'banh-cosy-marie-300g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Bánh quy Marie Cosy.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'chocopie-original-6-banh']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'ChocoPie Original 6 bánh']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'ChocoPie Original 6 bánh',
            'category_id' => $categoryIds['Bánh & kẹo'],
            'brand_id' => $brandIds['ChocoPie'],
            'price' => 35000,
            'sale_price' => 32000,
            'stock' => 45,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'chocopie-original-6-banh';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Bánh ChocoPie truyền thống.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'pocky-chocolate-47g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Pocky Chocolate 47g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Pocky Chocolate 47g',
            'category_id' => $categoryIds['Bánh & kẹo'],
            'brand_id' => $brandIds['Pocky'],
            'price' => 22000,
            'sale_price' => 20000,
            'stock' => 36,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'pocky-chocolate-47g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Bánh que Pocky chocolate.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'kitkat-2-fingers-17g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'KitKat 2 Fingers 17g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'KitKat 2 Fingers 17g',
            'category_id' => $categoryIds['Bánh & kẹo'],
            'brand_id' => $brandIds['KitKat'],
            'price' => 12000,
            'sale_price' => 10000,
            'stock' => 62,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'kitkat-2-fingers-17g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Thanh KitKat 2 Fingers.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'mentos-mint-37g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Kẹo Mentos Mint 37g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Kẹo Mentos Mint 37g',
            'category_id' => $categoryIds['Bánh & kẹo'],
            'brand_id' => $brandIds['Mentos'],
            'price' => 15000,
            'sale_price' => 13000,
            'stock' => 50,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'mentos-mint-37g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Kẹo Mentos bạc hà.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'alpenliebe-caramel-37g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Kẹo Alpenliebe Caramel 37g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Kẹo Alpenliebe Caramel 37g',
            'category_id' => $categoryIds['Bánh & kẹo'],
            'brand_id' => $brandIds['Alpenliebe'],
            'price' => 12000,
            'sale_price' => 10000,
            'stock' => 75,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'alpenliebe-caramel-37g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Kẹo Alpenliebe caramel.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'oreo-mini-chocolate-67g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Oreo Mini Chocolate 67g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Oreo Mini Chocolate 67g',
            'category_id' => $categoryIds['Bánh & kẹo'],
            'brand_id' => $brandIds['Oreo'],
            'price' => 18000,
            'sale_price' => 16000,
            'stock' => 52,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'oreo-mini-chocolate-67g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Oreo mini chocolate.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'oreo-vanilla-133g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Oreo Vanilla 133g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Oreo Vanilla 133g',
            'category_id' => $categoryIds['Bánh & kẹo'],
            'brand_id' => $brandIds['Oreo'],
            'price' => 28000,
            'sale_price' => 25000,
            'stock' => 42,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'oreo-vanilla-133g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Oreo vanilla.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'lays-tao-bien-50g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => "Lay's Vị Tảo Biển 50g"]);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => "Lay's Vị Tảo Biển 50g",
            'category_id' => $categoryIds['Snack'],
            'brand_id' => $brandIds["Lay's"],
            'price' => 16000,
            'sale_price' => 14000,
            'stock' => 70,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'lays-tao-bien-50g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = "Khoai tây Lay's tảo biển.";
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'lays-pho-mai-52g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => "Lay's Phô Mai 52g"]);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => "Lay's Phô Mai 52g",
            'category_id' => $categoryIds['Snack'],
            'brand_id' => $brandIds["Lay's"],
            'price' => 16000,
            'sale_price' => 14000,
            'stock' => 65,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'lays-pho-mai-52g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = "Khoai tây Lay's phô mai.";
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'oishi-bap-44g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Oishi Snack Bắp 44g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Oishi Snack Bắp 44g',
            'category_id' => $categoryIds['Snack'],
            'brand_id' => $brandIds['Oishi'],
            'price' => 10000,
            'sale_price' => 9000,
            'stock' => 88,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'oishi-bap-44g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Snack bắp Oishi.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'oishi-tom-cay-46g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Oishi Tôm Cay 46g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Oishi Tôm Cay 46g',
            'category_id' => $categoryIds['Snack'],
            'brand_id' => $brandIds['Oishi'],
            'price' => 10000,
            'sale_price' => 9000,
            'stock' => 80,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'oishi-tom-cay-46g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Snack tôm cay.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'poca-pho-mai-42g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Poca Phô Mai 42g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Poca Phô Mai 42g',
            'category_id' => $categoryIds['Snack'],
            'brand_id' => $brandIds['Poca'],
            'price' => 12000,
            'sale_price' => 10000,
            'stock' => 58,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'poca-pho-mai-42g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Snack Poca phô mai.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'poca-bo-nuong-42g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Poca Bò Nướng 42g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Poca Bò Nướng 42g',
            'category_id' => $categoryIds['Snack'],
            'brand_id' => $brandIds['Poca'],
            'price' => 12000,
            'sale_price' => 10000,
            'stock' => 57,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'poca-bo-nuong-42g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Snack Poca bò nướng.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'vinamilk-sua-tuoi-duong-180ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Vinamilk 100% Có Đường 180ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Vinamilk 100% Có Đường 180ml',
            'category_id' => $categoryIds['Sữa & dinh dưỡng'],
            'brand_id' => $brandIds['Vinamilk'],
            'price' => 7000,
            'sale_price' => 6500,
            'stock' => 120,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'vinamilk-sua-tuoi-duong-180ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Sữa tươi Vinamilk có đường.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'vinamilk-khong-duong-180ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Vinamilk Không Đường 180ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Vinamilk Không Đường 180ml',
            'category_id' => $categoryIds['Sữa & dinh dưỡng'],
            'brand_id' => $brandIds['Vinamilk'],
            'price' => 7000,
            'sale_price' => 6500,
            'stock' => 115,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'vinamilk-khong-duong-180ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Sữa tươi Vinamilk không đường.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'th-true-milk-duong-180ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'TH True Milk Có Đường 180ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'TH True Milk Có Đường 180ml',
            'category_id' => $categoryIds['Sữa & dinh dưỡng'],
            'brand_id' => $brandIds['TH True Milk'],
            'price' => 8000,
            'sale_price' => 7500,
            'stock' => 96,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'th-true-milk-duong-180ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Sữa tươi TH True Milk.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'th-true-milk-khong-duong-180ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'TH True Milk Không Đường 180ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'TH True Milk Không Đường 180ml',
            'category_id' => $categoryIds['Sữa & dinh dưỡng'],
            'brand_id' => $brandIds['TH True Milk'],
            'price' => 8000,
            'sale_price' => 7500,
            'stock' => 93,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'th-true-milk-khong-duong-180ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Sữa tươi TH True Milk không đường.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'milo-hop-180ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Milo Hộp 180ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Milo Hộp 180ml',
            'category_id' => $categoryIds['Sữa & dinh dưỡng'],
            'brand_id' => $brandIds['Milo'],
            'price' => 9000,
            'sale_price' => 8500,
            'stock' => 85,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'milo-hop-180ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Nước uống lúa mạch Milo.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'dutch-lady-180ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Dutch Lady 180ml Có Đường']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Dutch Lady 180ml Có Đường',
            'category_id' => $categoryIds['Sữa & dinh dưỡng'],
            'brand_id' => $brandIds['Dutch Lady'],
            'price' => 7000,
            'sale_price' => 6500,
            'stock' => 100,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'dutch-lady-180ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Sữa tươi Dutch Lady.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'nuoc-mam-nam-ngu-500ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Nước Mắm Nam Ngư 500ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Nước Mắm Nam Ngư 500ml',
            'category_id' => $categoryIds['Thực phẩm khô'],
            'brand_id' => $brandIds['Nam Ngư'],
            'price' => 32000,
            'sale_price' => 29000,
            'stock' => 44,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'nuoc-mam-nam-ngu-500ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Nước mắm Nam Ngư.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'nuoc-tuong-chinsu-500ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Nước Tương Chinsu 500ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Nước Tương Chinsu 500ml',
            'category_id' => $categoryIds['Thực phẩm khô'],
            'brand_id' => $brandIds['Chinsu'],
            'price' => 18000,
            'sale_price' => 16000,
            'stock' => 55,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'nuoc-tuong-chinsu-500ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Nước tương Chinsu.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'dau-an-neptune-1l']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Dầu Ăn Neptune 1L']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Dầu Ăn Neptune 1L',
            'category_id' => $categoryIds['Thực phẩm khô'],
            'brand_id' => $brandIds['Chinsu'],
            'price' => 45000,
            'sale_price' => 42000,
            'stock' => 35,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'dau-an-neptune-1l';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Dầu ăn Neptune 1L.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'hat-nem-knorr-thit-than-170g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Hạt Nêm Knorr Thịt Thăn 170g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Hạt Nêm Knorr Thịt Thăn 170g',
            'category_id' => $categoryIds['Thực phẩm khô'],
            'brand_id' => $brandIds['Knorr'],
            'price' => 25000,
            'sale_price' => 22000,
            'stock' => 40,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'hat-nem-knorr-thit-than-170g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Hạt nêm Knorr.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'bot-canh-chinsu-200g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Bột Canh Chinsu 200g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Bột Canh Chinsu 200g',
            'category_id' => $categoryIds['Thực phẩm khô'],
            'brand_id' => $brandIds['Chinsu'],
            'price' => 9000,
            'sale_price' => 8000,
            'stock' => 90,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'bot-canh-chinsu-200g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Bột canh Chinsu.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'tuong-ot-chinsu-250g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Tương Ớt Chinsu 250g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Tương Ớt Chinsu 250g',
            'category_id' => $categoryIds['Thực phẩm khô'],
            'brand_id' => $brandIds['Chinsu'],
            'price' => 13000,
            'sale_price' => 11000,
            'stock' => 70,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'tuong-ot-chinsu-250g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Tương ớt Chinsu.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'dau-goi-sunsilk-180ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Dầu Gội Sunsilk Óng Mượt 180ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Dầu Gội Sunsilk Óng Mượt 180ml',
            'category_id' => $categoryIds['Chăm sóc cá nhân'],
            'brand_id' => $brandIds['Sunsilk'],
            'price' => 39000,
            'sale_price' => 35000,
            'stock' => 38,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'dau-goi-sunsilk-180ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Dầu gội Sunsilk.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'dau-goi-head-shoulders-170ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Dầu Gội Head & Shoulders 170ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Dầu Gội Head & Shoulders 170ml',
            'category_id' => $categoryIds['Chăm sóc cá nhân'],
            'brand_id' => $brandIds['Head & Shoulders'],
            'price' => 62000,
            'sale_price' => 55000,
            'stock' => 31,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'dau-goi-head-shoulders-170ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Dầu gội Head & Shoulders.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'sua-tam-dove-250g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Sữa Tắm Dove 250g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Sữa Tắm Dove 250g',
            'category_id' => $categoryIds['Chăm sóc cá nhân'],
            'brand_id' => $brandIds['Dove'],
            'price' => 65000,
            'sale_price' => 59000,
            'stock' => 27,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'sua-tam-dove-250g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Sữa tắm Dove.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'sua-tam-lifebuoy-250ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Sữa Tắm Lifebuoy 250ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Sữa Tắm Lifebuoy 250ml',
            'category_id' => $categoryIds['Chăm sóc cá nhân'],
            'brand_id' => $brandIds['Lifebuoy'],
            'price' => 45000,
            'sale_price' => 42000,
            'stock' => 44,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'sua-tam-lifebuoy-250ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Sữa tắm Lifebuoy.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'kem-danh-rang-ps-150g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Kem Đánh Răng P/S 150g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Kem Đánh Răng P/S 150g',
            'category_id' => $categoryIds['Chăm sóc cá nhân'],
            'brand_id' => $brandIds['P/S'],
            'price' => 36000,
            'sale_price' => 32000,
            'stock' => 50,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'kem-danh-rang-ps-150g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Kem đánh răng P/S.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'kem-danh-rang-colgate-150g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Kem Đánh Răng Colgate 150g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Kem Đánh Răng Colgate 150g',
            'category_id' => $categoryIds['Chăm sóc cá nhân'],
            'brand_id' => $brandIds['Colgate'],
            'price' => 39000,
            'sale_price' => 35000,
            'stock' => 43,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'kem-danh-rang-colgate-150g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Kem đánh răng Colgate.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'nuoc-giat-omo-800g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Nước Giặt OMO 800g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Nước Giặt OMO 800g',
            'category_id' => $categoryIds['Gia dụng'],
            'brand_id' => $brandIds['OMO'],
            'price' => 62000,
            'sale_price' => 56000,
            'stock' => 33,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'nuoc-giat-omo-800g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Nước giặt OMO.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'nuoc-rua-chen-sunlight-750ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Nước Rửa Chén Sunlight 750ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Nước Rửa Chén Sunlight 750ml',
            'category_id' => $categoryIds['Gia dụng'],
            'brand_id' => $brandIds['Sunlight'],
            'price' => 32000,
            'sale_price' => 29000,
            'stock' => 52,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'nuoc-rua-chen-sunlight-750ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Nước rửa chén Sunlight.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'nuoc-xa-comfort-900ml']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Nước Xả Comfort 900ml']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Nước Xả Comfort 900ml',
            'category_id' => $categoryIds['Gia dụng'],
            'brand_id' => $brandIds['Comfort'],
            'price' => 49000,
            'sale_price' => 45000,
            'stock' => 29,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'nuoc-xa-comfort-900ml';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Nước xả Comfort.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'bot-giat-omo-800g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Bột Giặt OMO 800g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Bột Giặt OMO 800g',
            'category_id' => $categoryIds['Gia dụng'],
            'brand_id' => $brandIds['OMO'],
            'price' => 58000,
            'sale_price' => 52000,
            'stock' => 36,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'bot-giat-omo-800g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Bột giặt OMO.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'nuoc-lau-san-gift-1l']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Nước Lau Sàn Gift 1L']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Nước Lau Sàn Gift 1L',
            'category_id' => $categoryIds['Gia dụng'],
            'brand_id' => $brandIds['Gift'],
            'price' => 38000,
            'sale_price' => 34000,
            'stock' => 25,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'nuoc-lau-san-gift-1l';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Nước lau sàn Gift.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'khan-giay-gift-180-to']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Khăn Giấy Hộp Gift 180 tờ']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Khăn Giấy Hộp Gift 180 tờ',
            'category_id' => $categoryIds['Gia dụng'],
            'brand_id' => $brandIds['Gift'],
            'price' => 27000,
            'sale_price' => 24000,
            'stock' => 45,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'khan-giay-gift-180-to';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Khăn giấy Gift.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'xuc-xich-cp-pho-mai-175g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Xúc Xích CP Phô Mai 175g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Xúc Xích CP Phô Mai 175g',
            'category_id' => $categoryIds['Đồ ăn nhanh'],
            'brand_id' => $brandIds['CP'],
            'price' => 35000,
            'sale_price' => 32000,
            'stock' => 40,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'xuc-xich-cp-pho-mai-175g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Xúc xích CP phô mai.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'xuc-xich-vissan-200g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Xúc Xích Vissan 200g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Xúc Xích Vissan 200g',
            'category_id' => $categoryIds['Đồ ăn nhanh'],
            'brand_id' => $brandIds['Vissan'],
            'price' => 38000,
            'sale_price' => 35000,
            'stock' => 38,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'xuc-xich-vissan-200g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Xúc xích Vissan.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'ca-vien-cp-200g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Cá Viên CP 200g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Cá Viên CP 200g',
            'category_id' => $categoryIds['Đồ ăn nhanh'],
            'brand_id' => $brandIds['CP'],
            'price' => 42000,
            'sale_price' => 39000,
            'stock' => 30,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'ca-vien-cp-200g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Cá viên CP.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    if ($findSlug) {
        $findSlug->execute(['slug' => 'bo-vien-vissan-175g']);
        $exists = $findSlug->fetchColumn();
    } else {
        $exists = false;
    }

    if ($exists === false) {
        $findName->execute(['name' => 'Bò Viên Vissan 175g']);
        $exists = $findName->fetchColumn();
    }

    if ($exists !== false) {
        $skipped++;
    } else {
        $cols = ['name','category_id','brand_id','price','sale_price','stock','status'];
        $vals = [':name',':category_id',':brand_id',':price',':sale_price',':stock',"'active'"];
        $params = [
            'name' => 'Bò Viên Vissan 175g',
            'category_id' => $categoryIds['Đồ ăn nhanh'],
            'brand_id' => $brandIds['Vissan'],
            'price' => 45000,
            'sale_price' => 41000,
            'stock' => 26,
        ];
        if (isset($columns['slug'])) {
            $cols[] = 'slug';
            $vals[] = ':slug';
            $params['slug'] = 'bo-vien-vissan-175g';
        }
        if (isset($columns['description'])) {
            $cols[] = 'description';
            $vals[] = ':description';
            $params['description'] = 'Bò viên Vissan.';
        }
        $sql = "INSERT INTO products (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $inserted++;
    }

    $pdo->commit();
    $messages[] = "Đã thêm $inserted sản phẩm mới.";
    $messages[] = "Bỏ qua $skipped sản phẩm đã tồn tại.";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seed Catalog - Nhà Mình Mart</title>
<style>
body{margin:0;padding:40px 20px;font-family:Arial,sans-serif;background:#f5f7fa;color:#1f2937}
.box{width:min(900px,100%);margin:auto;background:#fff;border-radius:18px;padding:30px;box-shadow:0 18px 50px rgba(0,0,0,.08)}
h1{margin-top:0}
.ok{background:#ecfdf3;border:1px solid #b7ebcc;color:#166534;padding:14px;border-radius:12px;margin-bottom:10px}
.err{background:#fff1f2;border:1px solid #fecdd3;color:#991b1b;padding:14px;border-radius:12px}
.note{margin-top:20px;padding:15px;background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;line-height:1.6}
</style>
</head>
<body>
<div class="box">
<h1>🏪 Nhà Mình Mart — Seed Catalog</h1>
<?php foreach ($messages as $m): ?>
<div class="ok">✅ <?= htmlspecialchars($m, ENT_QUOTES, 'UTF-8') ?></div>
<?php endforeach; ?>
<?php if ($error !== ''): ?>
<div class="err">❌ <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<div class="note">
<strong>Catalog đã được mở rộng.</strong><br><br>
Kiểm tra <code>products.php</code> và Admin → Sản phẩm.<br>
Sau khi kiểm tra xong, hãy xóa file <code>seed_store_catalog.php</code>.
</div>
</div>
</body>
</html>
