<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//searchAll
$app->get('/food', function (Request $request, Response $response) {
    $conn = $GLOBALS['conn'];
    $sql = 'select food.fid, food.name,food.price,food.img,
    type.name as type from food inner join type on food.type = type.tid';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    foreach ($result as $row) {
        array_push($data, $row);
    }

    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus(200);
});

//searchByTypename
$app->get('/food/typename/{type}', function (Request $request, Response $response, $args) {
    $conn = $GLOBALS['conn'];

    $sql = 'select food.fid, food.name,food.price,food.img,
    type.name as type from food inner join type on food.type = type.tid where type.name like ?';
    $stmt = $conn->prepare($sql);
    $name = '%' . $args['type'] . '%';
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    foreach ($result as $row) {
        array_push($data, $row);
    }

    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus(200);
});

//searchByTypeID
$app->get('/food/type/{type}', function (Request $request, Response $response, $args) {
    $conn = $GLOBALS['conn'];
    
    $sql = 'SELECT food.fid, food.name,food.price,food.img,
    type.name as type from food inner join type on food.type = type.tid where food.type = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $args['type']);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    foreach ($result as $row) {
        array_push($data, $row);
    }

    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus(200);
});

//searchByFoodID
$app->get('/food/{fid}', function (Request $request, Response $response, $args) {
    $fid = $args['fid'];
    $conn = $GLOBALS['conn'];
    $sql = 'select food.fid, food.name,food.detail,food.url,
    type.name as type from food inner join type on food.type = type.tid where food.fid = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $fid);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        array_push($data, $row);
    }
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus(200);
});

//addFood
$app->post('/food', function (Request $request, Response $response, $args) {
    $json = $request->getBody();
    $jsonData = json_decode($json, true);

    $conn = $GLOBALS['conn'];
    $sql = 'insert into food (name, price, img, type) values (?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sisi', $jsonData['name'], $jsonData['price'], $jsonData['img'], $jsonData['type']);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    if ($affected > 0) {

        $data = ["affected_rows" => $affected, "last_fid" => $conn->insert_id];
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
});

//editFood
$app->put('/food/{id}', function (Request $request, Response $response, $args) {
    $json = $request->getBody();
    $jsonData = json_decode($json, true);
    $id = $args['id'];
    $conn = $GLOBALS['conn'];
    $sql = 'update food set name=?, price=?, type=?, img=? where fid = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('siisi', $jsonData['name'], $jsonData['price'], $jsonData['type'], $jsonData['img'], $id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    if ($affected > 0) {
        $data = ["affected_rows" => $affected];
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
});

//deleteFood
$app->delete('/food/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];
    $conn = $GLOBALS['conn'];
    $sql = 'delete from food where fid = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    if ($affected > 0) {
        $data = ["affected_rows" => $affected];
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
});
