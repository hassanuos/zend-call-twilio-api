<?php
/**
 * Zend Framework (http://framework.zend.com/)
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * Author: Hassan Raza 
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Services_Twilio;
use Services_Twilio_Twiml;

class TwilioServiceController extends AbstractController{

		public function testcallAction(){
			      
            $sid = 'SID';
            $token = 'TOKEN';
            $client = new Services_Twilio($sid, $token);
            
			try {
			
			  $call = $client->account->calls->create(
				  	"+17483748743",	      //From Call 								
				  	"+12343444434",       //To Call
				  	"http://www.domain.com/api/service/SpeakSentences" // Your ApplicationSid
				);
			
				echo "You Caller ID : $call->sid";
			
			} catch (Exception $e) {
			
				echo 'Error starting phone call: ' . $e->getMessage() . "\n";
			
			}
		}
		public function SpeakSentencesAction(){
		
			$response = new Services_Twilio_Twiml();
			
			$gather = $response->gather(array(
				'action' => 'http://www.domain.com/api/service/ProcessRecDigit',
				'method' => 'GET',
				'numDigits' => '1'
			));
			
			/*These Sentences Will Be Say On Mobile Phone*/
			
			$gather->say("Wellcom to zong call center services.");
			$gather->say("If You Would Like to enable SMS Press 1");
			$gather->say("If You Would Like to enable Internet Press 2");
			$gather->say("If You Would Like to enable Voicemail Press 3");
			$gather->say("If You Would Like to Quit Press 4");
			
			/* Get/Send Response In XML */
			header('Content-Type: text/xml');
			print $response;
			exit;
		}
		public function ProcessRecDigitAction(){
			$digit = isset($_REQUEST['Digits']) ? $_REQUEST['Digits'] : null;
			$choices = array(
				'1' => 'SMS',
				'2' => 'Internet',
				'3' => 'Voicemail',
				'4' => 'Quit',
			);
			if (isset($choices[$digit])) {
				$say = 'You Ordered '.$choices[$digit].' Thank you. Your choice has been tallied.';
			} else {
				$say = "Sorry, I don't have that topping.";
			}
			$response = new Services_Twilio_Twiml();
			$response->say($say);              //Say Response Message
			$response->hangup();               //For End Calling
			
			/* Get/Send Response In XML */
			
			header('Content-Type: text/xml');
			print $response;
			exit;
		}
}
