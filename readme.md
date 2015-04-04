#Mail
Class of email sending to PHP

* Renders external templates
* Validates data
* Fires email
* Returns errors

##License

####Copyright 2015 PHILIPPE ASSIS

Licensed under the Apache License, Version 2.0 (the “License”); you may not use this file except in compliance with the License. You may obtain a copy of the License at http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an “AS IS” BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.

##Example
Example below was created in order to receive instructions in json

###instantiating

    include 'mail/mail.php';
    $mail = new Mail();
    
###Adding Data

    $mail->post($_POST);
    
###Requirements

    $mail->required('name', 'You did not enter your email.', 'Invalid email');
    
    $mail->required('message', 'You must enter a message.');
    
###Filters
    
    $trim = function ($key, $post) use ($mail) {
        $mail->post($key, trim($post));
    };
    
    $mail->filter($trim);
    
###Validating Data

    if (!$mail->handling()) {
        $mail->jsonHeader();
        echo json_encode($mail->error());
        exit;
    }
    
###Send Settings    
    $mail->from = $mail->post('email');
    $mail->to = 'assis@philippeassis.com';
    $mail->subject = 'Contato pelo site: ' . $mail->post('name');
    
###Generating email body from an external model
    $mail->body('templates/email.php', ['msg' => $mail->post('msg'), 'logo' => $mail->base64('img/basico.jpg'), 'footer' => false]);
    
###Sending email
    if (!$mail->send()) {
        echo json_encode(['error' => true, 'success' => false, 'msg' =>  'Mensagem não enviada.']);
        exit;
    }


##Application Ajax Javascript for the Frontend
You poderar use sendmail () to send your email frontend with jquery ajax and.

####js

    $('form').sendmail({
        url : 'contact.php'
    })

####html
Example created using Bootstrap:

    <form id="contat-form">
        <div class="form-group">
            <input class="form-control input-lg" type="email" name="email" placeholder="Email">
        </div>
        <div class="form-group">
            <textarea class="form-control input-lg" name="message" placeholder="Message"></textarea>
        </div>
        <div class="form-group">
            <label>Are you human? Enter which letters you see in the picture</label>
        </div>
        <div class="col-xs-6">
            <div class="form-group text-center captcha">
                <img src="contacts.php">
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                <input class="form-control input-lg" type="input" name="captcha" title="Type the letters you see on the image" required>
            </div>
        </div>
        <div class="form-group">
            <button class="btn btn-theme btn-lg">Send</button>
        </div>
    </form>
    
sendmail.js and a minimized version, is found in mail/js

##PhilippeAssis/captcha
If you are using [PhilippeAssis/captcha](https://github.com/PhilippeAssis/captcha), work the image and validation in the same way the Mail().

    $captcha = include 'captcha.php';
    
    if (!$captcha) {
        echo json_encode(['error' => true, 'success' => false, 'msg' => 'A verificação não confere.', 'captcha' => true]);
        exit;
    }
    
    // ... after the Mail() controller ...

Thus, sendmail plugin () worked perfectly changing the image of captach case return an error.