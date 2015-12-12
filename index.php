<?php
        
/*
 [
                {
                    id: 1, title: 'Домашние дела', tasks:
                    [
                        { id: 1, text: 'Купить батон', isComplite: false },
                        { id: 2, text: 'Помыть посуду', isComplite: true },
                    ]
                },
                {
                    id: 2, title: 'Рабочие дела', tasks:
                    [
                        { id: 1, text: 'Посторить дом', isComplite: false },
                        { id: 2, text: 'Вырастить сына', isComplite: false },
                    ]
                }
            ];
 */

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
        && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {    
    // Если к нам идёт Ajax запрос, то ловим его        
        $action = $_POST['action'];
        $data = $_POST['data'];
        $m = new MyConfig();
        $m->openbd();
        switch ($action){
            case 'GetMaxIdBlock':{echo $m->GetMaxIdBlock();break;}
            case 'GetMaxIdLine':{echo $m->GetMaxIdLine($data);break;}
            case 'Start':{$r=$m->GetFulldata();echo json_encode($r);break;}
            case 'myDeleteLine':{echo $m->DeleteLine($data);break;}
            case 'myDeleteBlock':{echo $m->DeleteBlock($data);break;}
            case 'updateLine':{echo $m->updateLine($data);break;}
            case 'updateBlock':{echo $m->updateBlock($data);break;}
        }
    exit;
}
//Если это не ajax запрос
echo 'Это не ajax запрос!';
exit;
class MyConfig{
    function  openbd(){
        //mysql_connect("mysql.hostinger.ru","u881402613_alex","Q1w2e3r4t5");
        mysql_connect("localhost","root","");
        mysql_select_db("u881402613_book");
    }    
    function colsedb(){
        mysql_close();
    }    
    function getUserId(){
        //$sql = "Select User_id from Users where SID like '".$_COOKIE["coosid"]."'";
		
	//$result = mysql_query($sql);		
	//$User_id = mysql_result($result,0,'User_id');
        return 1;
    }    
    function AddData($r){
	$User_id = $this->getUserId();	
	$sql = "INSERT INTO tableblock(user_id, `title`)"
                . "VALUES (".$User_id.",'".$r->title."')";
	mysql_query($sql);
    }
    //Делает вставку в табилцу tableblock (бронирование записи)
    function GetMaxIdBlock(){
	$User_id = $this->getUserId();
	$sql = "INSERT INTO tableblock(user_id, `title`)"
                . " VALUES (".$User_id.",'')";
        mysql_query($sql);
	return mysql_insert_id();
    }
    function GetMaxIdLine($idBlock){	
	$sql = "INSERT INTO tabletask(idtableblock, `text`,orderdate,isComplite)"
                . " VALUES (".$idBlock.",'',NOW(),0)";
        mysql_query($sql);
	return mysql_insert_id();
    }
    function GetFulldata(){
        $User_id = $this->getUserId();
        
        $sql = " SELECT tb.idtableblock,tb.title,".
                        " tt.idtableTask,tt.isComplite,tt.`text`".
              " FROM tableblock as tb left join tabletask tt on tb.idtableblock=tt.idtableblock".
             " where tb.user_id = ".$User_id.
             " order by tb.idtableblock,tt.idtableTask";

	$result = mysql_query($sql);
        $resultData = array();
        $tasks = array();
        $idblock = 0;
        $title = 0;
        
        while ($row = mysql_fetch_assoc($result)) {            
            if($idblock != $row['idtableblock']){
                if($idblock != 0){                    
                    $block = array("id"=> $idblock,"title"=>$title,"tasks"=>$tasks);
                    array_push($resultData,$block);
                }                
                $idblock = $row['idtableblock'];
                $title = $row['title'];
                if(isset($row['idtableTask'])){
                    $tasks = array(array("id"=>$row['idtableTask'],"text"=>$row['text'],"isComplite"=>$row['isComplite']==1));
                }
            }
            else{
                $task = array("id"=>$row['idtableTask'],"text"=>$row['text'],"isComplite"=>$row['isComplite']==1);
                array_push($tasks,$task);
            }                
	}
        $block = array("id"=> $idblock,"title"=>$title,"tasks"=>$tasks);
        array_push($resultData,$block);
        //        $r = array(
//                array(
//                    "id"=> 1,
//                    "title"=>"Домашние дела",
//                    "tasks"=>
//                        array(
//                            array(
//                                "id"=>1,
//                                "text"=>"Купить батон",
//                                "isComplite"=>false,
//                                ),
//                            array(
//                                "id"=>2,
//                                "text"=>"Помыть посуду",
//                                "isComplite"=>true
//                                )
//                            )
//                    ),
//                array(
//                    "id"=>2,
//                    "title"=>"Рабочие дела",
//                    "tasks"=>
//                    array(
//                        array(
//                            "id"=>1,
//                            "text"=>"Посторить дом",
//                            "isComplite"=>false
//                            ),
//                        array(
//                            "id"=>2,
//                            "text"=>"Вырастить сына",
//                            "isComplite"=>false,
//                            )
//                        )
//                    )
//            );
        mysql_free_result($result);
        
        return $resultData;
    }
    function DeleteLine($idtableTask){
        $sql = " delete FROM tabletask".
             " where idtableTask = ".$idtableTask;

	$result = mysql_query($sql);
        $stat='not';
        if($result>0){
            $stat='Ok';
        }
        return $stat;
    }
    function DeleteBlock($idtableblock){
        $sql = " delete FROM tabletask".
             " where idtableblock = ".$idtableblock;
	$result = mysql_query($sql);
        $stat='not';
        if($result>0){
            $stat='Ok';
        }
        $sql = " delete FROM tableblock ".
             " where idtableblock = ".$idtableblock;
	$result = mysql_query($sql);        
        if($result>0){
            $stat.=' Ok';
        }
        else{
            $stat.=' not';
        }
        return $stat;
    }
    function updateLine($data){
        $d = json_decode($data);
        //$d = $data;
        $text = $d->text;
        $isComplite = $d->isComplite?1:0;
        $idtableTask= $d->id;
        $sql = " update tabletask set DateChange=NOW(),text ='".$text."',isComplite = ".$isComplite.
             " where idtableTask = ".$idtableTask;
        $result = mysql_query($sql);
        $stat='not';
        if($result>0){
            $stat='ok';
        }
        else{
            $stat=' not ';
        }
        return $stat;
    }
    function updateBlock($data){
        $d = json_decode($data);
        //$d = $data;
        $title = $d->title;        
        $idtableBlock= $d->id;
        $sql = " update tableblock set title ='".$title."'".
             " where idtableblock = ".$idtableBlock;
        $result = mysql_query($sql);
        $stat='not';
        if($result>0){
            $stat=' Ok';
        }
        else{
            $stat=' not ';
        }
        return $stat;
    }
}
?>