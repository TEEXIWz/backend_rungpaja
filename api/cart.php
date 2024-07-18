<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

//searchAll
$app->get('/cart', function (Request $request, Response $response) {
    $conn = $GLOBALS['conn'];
    $sql = 'SELECT * FROM orderamount';
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

//show itemcart
$app->get('/cart/{cusID}', function (Request $request, Response $response, $args) {
    $conn = $GLOBALS['conn'];
    $cusID = $args['cusID'];
    $sql = 'SELECT      ROW_NUMBER() OVER(ORDER BY fid) AS num,food.fid, name, img, amount, sum(amount*price) as totalPrice
            FROM        orderamount
            INNER JOIN  food
            ON          orderamount.fid=food.fid 
            WHERE       cusID = ?
            AND         oid is null
            GROUP BY    food.fid, name, img, amount';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $cusID);
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

//add item
$app->post('/addToCart/{cusID}/{fid}', function (Request $request, Response $response, $args) {
    $conn = $GLOBALS['conn'];
    $fid = $args['fid'];
    $cusID = $args['cusID'];

    $sql = 'insert into orderamount (fid, cusID, amount) values (?, ?, 1)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $fid, $cusID);
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

//remove item
$app->delete('/removeItem/{cusID}/{fid}', function (Request $request, Response $response, $args) {
    $conn = $GLOBALS['conn'];
    $fid = $args['fid'];
    $cusID = $args['cusID'];

    $sql = 'DELETE 
            FROM    orderamount 
            WHERE   fid = ?
            AND     cusID = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $fid, $cusID);
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

//increase
$app->put('/increaseItem/{cusID}/{fid}', function (Request $request, Response $response, $args) {
    $conn = $GLOBALS['conn'];
    $fid = $args['fid'];
    $cusID = $args['cusID'];

    $sql = 'UPDATE  orderamount 
            SET     amount=amount+1 
            WHERE   fid = ?
            AND     cusID = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $fid, $cusID);
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

//decrease
$app->put('/decreaseItem/{cusID}/{fid}', function (Request $request, Response $response, $args) {
    $conn = $GLOBALS['conn'];
    $fid = $args['fid'];
    $cusID = $args['cusID'];

    $sql = 'UPDATE  orderamount 
            SET     amount=amount-1 
            WHERE   fid = ?
            AND     cusID = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $fid, $cusID);
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

$app->put('/updateOrder/{cusID}/{oid}', function (Request $request, Response $response, $args) {
    $conn = $GLOBALS['conn'];
    $oid = $args['oid'];
    $cusID = $args['cusID'];

    $sql = 'UPDATE  orderamount 
            SET     oid = ?
            WHERE   cusID = ?
            AND     oid is null';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $oid, $cusID);
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