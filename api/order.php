<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//searchAll
$app->get('/orderAll', function (Request $request, Response $response) {
    $conn = $GLOBALS['conn'];
    $sql = 'SELECT      ROW_NUMBER() OVER(ORDER BY oid) AS num, oid, cusID, name, address, detail, place, iorder.phone, pay, totalPrice, odate, fdate, iorder.status 
            FROM        iorder 
            -- INNER JOIN  user 
            -- ON          iorder.cusID = user.uid
            ORDER BY    num desc';
    // $sql = 'SELECT * FROM iorder';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    foreach ($result as $row) {
        array_push($data, $row);
    }

    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus(200);
});

//search order delivered
$app->get('/order', function (Request $request, Response $response) {
    $conn = $GLOBALS['conn'];
    $sql = 'SELECT      ROW_NUMBER() OVER(ORDER BY oid) AS num, oid, cusID, name, address, detail, place, iorder.phone, pay, totalPrice, odate, fdate, iorder.status 
            FROM        iorder 
            -- INNER JOIN  user 
            -- ON          iorder.cusID    = user.uid
            WHERE       iorder.status   = 2
            ORDER BY    num desc';
    // $sql = 'SELECT * FROM iorder';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    foreach ($result as $row) {
        array_push($data, $row);
    }

    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus(200);
});

//search order undelivered
$app->get('/newOrder', function (Request $request, Response $response) {
    $conn = $GLOBALS['conn'];
    $sql = 'SELECT      ROW_NUMBER() OVER(ORDER BY oid) AS num, oid, cusID, name, address, detail, place, iorder.phone, pay, totalPrice, odate, fdate, iorder.status 
            FROM        iorder 
            -- INNER JOIN  user 
            -- ON          iorder.cusID    = user.uid
            WHERE       iorder.status   = 1
            ORDER BY    num desc';
    // $sql = 'SELECT * FROM iorder';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    foreach ($result as $row) {
        array_push($data, $row);
    }

    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus(200);
});

//searchByCusID
$app->get('/order/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];
    $conn = $GLOBALS['conn'];
    $sql = 'SELECT      ROW_NUMBER() OVER(ORDER BY oid) AS num, oid, cusID, name, address, detail, place, iorder.phone, pay, totalPrice, odate, fdate, iorder.status 
            FROM        iorder
            -- INNER JOIN  user
            -- ON          iorder.cusID    = user.uid
            WHERE       cusID           = ?
            ORDER BY    num desc';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    foreach ($result as $row) {
        array_push($data, $row);
    }

    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus(200);
});

//searchByCusID
$app->post('/orderid', function (Request $request, Response $response) {
    $json = $request->getBody();
    $jsonData = json_decode($json, true);

    $conn = $GLOBALS['conn'];
    $sql = 'SELECT      ROW_NUMBER() OVER(ORDER BY oid) AS num, oid, cusID, name, address, detail, place, iorder.phone, pay, totalPrice, odate, fdate, iorder.status 
            FROM        iorder 
            WHERE       cusID           = ?
            ORDER BY    num desc';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $jsonData['cid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    foreach ($result as $row) {
        array_push($data, $row);
    }

    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus(200);
});

//addOrder
$app->post('/order', function (Request $request, Response $response) {
    $json = $request->getBody();
    $jsonData = json_decode($json, true);

    $conn = $GLOBALS['conn'];
    $sql = 'INSERT INTO iorder (cusID, name, address, detail, place, phone, pay, totalPrice, odate, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 1)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issssssi', $jsonData['cusID'],$jsonData['name'], $jsonData['address'], $jsonData['detail'], $jsonData['place'], $jsonData['phone'],$jsonData['pay'], $jsonData['totalPrice']);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    if ($affected > 0) {
        // $data = ["affected_rows" => $affected, "last_oid" => $conn->insert_id];
        $response->getBody()->write(json_encode($conn->insert_id, JSON_UNESCAPED_UNICODE));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
});

//Cancel order
$app->post('/order/cancel/{oid}', function (Request $request, Response $response) {
    $json = $request->getBody();
    $jsonData = json_decode($json, true);

    $conn = $GLOBALS['conn'];
    $sql = 'INSERT INTO iorder (cusID, address, detail, place, pay, totalPrice, odate, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 1)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('issssi', $jsonData['cusID'], $jsonData['address'], $jsonData['detail'], $jsonData['place'], $jsonData['pay'], $jsonData['totalPrice']);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    if ($affected > 0) {
        $data = ["affected_rows" => $affected, "last_oid" => $conn->insert_id];
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
});

//update status
$app->put('/order/{oid}/{status}', function (Request $request, Response $response, $args) {
    $conn = $GLOBALS['conn'];
    $oid = $args['oid'];
    $status = $args['status'];
    $sql = 'UPDATE iorder
            SET status = ?
            WHERE oid = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $status, $oid);
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

//show item order
$app->get('/orderItem/{oid}', function (Request $request, Response $response, $args) {
    $conn = $GLOBALS['conn'];
    $oid = $args['oid'];
    $sql = 'SELECT      food.fid, name, img, amount, sum(amount*price) as totalPrice
            FROM        orderamount
            INNER JOIN  food 
            ON          orderamount.fid=food.fid 
            WHERE       oid = ?
            GROUP BY    food.fid, name, img, amount';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $oid);
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