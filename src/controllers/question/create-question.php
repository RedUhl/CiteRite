<?php
 require_once('functions/createQuestion.php');
 require_once('functions/deleteQuestion.php');
 require_once('functions/publishQuiz.php');
 require_once('functions/countQuestions.php');
 require_once('functions/getQuestions.php');

 $postStatus = $app->request->post('postQuiz');
 $answer = $app->request->post('answer');
 $prob = $app->request->post('problem');
 $info = $app->request->post('info');
 $questNum = $app->request->post('questNum');
 $questId = $app->request->post('questId');

 $info = str_replace("<div>", "", $info);
 $info  = str_replace("</div>", "", $info);

 $answer = str_replace("<div>", "",  $answer);
 $answer = str_replace("</div>", "",  $answer);

 $prob = str_replace("<div>", "",  $prob);
 $prob = str_replace("</div>", "",  $prob);
 
 $questionNumber = countQuestions($db, $id);
 $numberOfQuestions = json_decode($questionNumber, true);
 $numberOfQuestions = $numberOfQuestions['COUNT(quiz_id)'] + 1; //for new question
 $questionsInQuiz = getAllQuests($db, $id);

 switch($postStatus){
     case "publish":
         createQuestion($db, $id, $answer, $prob, $info);
         $questId = $db->lastInsertId(); 
         publishQuiz($db, $id);
         $app->response->redirect("/professor-dashboard/quiz/question/". $questId. "/update");
         break;
      case "updateQuestionAndPublish":
         publishQuiz($db, $id);
         saveQuestion($db, $id, $questId,  $answer, $prob, $info);
         $app->response->redirect("/professor-dashboard/quiz/question/". $questId. "/update");
         break;
     case "saveQuestion":
         saveQuestion($db, $id, $questId,  $answer, $prob, $info);
      
         $app->response->redirect("/professor-dashboard/quiz/". $id. "/question/create");
         break;
      case "updateQuestion":
         saveQuestion($db, $id, $questId,  $answer, $prob, $info);
      
         $app->response->redirect("/professor-dashboard/quiz/question/". $questId. "/update");
         break;
      case 'saveDraft' :
      createQuestion($db, $id, $answer, $prob, $info);
         $questId = $db->lastInsertId(); 
         $app->response->redirect("/professor-dashboard/quiz/question/". $questId. "/update");
         break;
     case 'addQuestion':
        createQuestion($db, $id, $answer, $prob, $info);
        $app->response->redirect("/professor-dashboard/quiz/". $id. "/question/create");
        break;

     case 'deleteQuestion':
         deleteQuestion($db, $id, $questId);
         $questId = getLastKnownQuestion($db, $id);
         
         if($questId != null){
            $app->response->redirect("/professor-dashboard/quiz/question/". $questId. "/update");
         } else {
            $app->response->redirect("/professor-dashboard/quiz/". $id. "/question/create");

         }
         
         break;
 }
    
?>