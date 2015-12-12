<?php
        
/*
 [
                {
                    id: 1, title: 'Домашние дела', tasks:
                    [
                        { id: 1, text: 'Купить батон', isComplite: false },
                        { id: 2, text: 'Помыть посуду', isComplite: true },
                    ],
                    maxid:3
                },
                {
                    id: 2, title: 'Рабочие дела', tasks:
                    [
                        { id: 1, text: 'Посторить дом', isComplite: false },
                        { id: 2, text: 'Вырастить сына', isComplite: false },
                    ],
                    maxid:3
                }
            ];
 */

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
        && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Если к нам идёт Ajax запрос, то ловим его        
        $json = $_POST['id'];
        //$r = json_decode($json);
        
        $r = array(
                array(
                    "id"=> 1,
                    "title"=>"Домашние дела",
                    "tasks"=>
                        array(
                            array(
                                "id"=>1,
                                "text"=>"Купить батон",
                                "isComplite"=>false,
                                ),
                            array(
                                "id"=>2,
                                "text"=>"Помыть посуду",
                                "isComplite"=>true
                                )
                            ),
                    "maxid"=>3
                    ),
                array(
                    "id"=>2,
                    "title"=>"Рабочие дела",
                    "tasks"=>
                    array(
                        array(
                            "id"=>1,
                            "text"=>"Посторить дом",
                            "isComplite"=>false
                            ),
                        array(
                            "id"=>2,
                            "text"=>"Вырастить сына",
                            "isComplite"=>false,
                            )
                        ),
                    "maxid"=>3,
                    )
            );
        //echo var_dump($r);
        //$r[0]->id = 1000;
        echo json_encode($r);
    exit;
}
//Если это не ajax запрос
echo 'Это не ajax запрос!';

        
?>