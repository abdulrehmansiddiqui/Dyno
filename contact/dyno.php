<?PHP
/*
Simfatic Forms Main Form processor script

This script does all the server side processing. 
(Displaying the form, processing form submissions,
displaying errors, making CAPTCHA image, and so on.) 

All pages (including the form page) are displayed using 
templates in the 'templ' sub folder. 

The overall structure is that of a list of modules. Depending on the 
arguments (POST/GET) passed to the script, the modules process in sequence. 

Please note that just appending  a header and footer to this script won't work.
To embed the form, use the 'Copy & Paste' code in the 'Take the Code' page. 
To extend the functionality, see 'Extension Modules' in the help.

*/
require_once("./includes/dyno-lib.php");
$formmailobj =  new FormMail("dyno");
$formmailobj->setFormPage(sfm_readfile("./templ/dyno_form_page.txt"));
$formmailobj->setFormID("bdd3603e-fab5-4739-8c35-812ac1f230bb");
$formmailobj->setFormKey("0da3dd19-602e-4a4b-af4d-5e085c7685c9");
$formmailobj->setEmailFormatHTML(true);
$formmailobj->EnableLogging(true);
$formmailobj->SetDebugMode(true);
$formmailobj->SetFromAddress("Dyno Treats<info@dynotreats.com>");
$formmailobj->SetCommonDateFormat("m-d-Y");
$formmailobj->SetSingleBoxErrorDisplay(true);
$formmailobj->InitSMTP("mail.dynotreats.com","info@dynotreats.com","C8324BA816E8B2AEE8FDC89669C809B6",25);
$fm_installer =  new FM_FormInstaller();
$formmailobj->addModule($fm_installer);

$formmailobj->setIsInstalled(true);
$formmailobj->setFormFileFolder("./formdata");
$sfm_captcha =  new FM_CaptchaCreator("Captcha");
$sfm_captcha->SetSize(150,60);
$sfm_captcha->SetCharset("2356789ABCDEFGHJKLMNPQRSTUVWXYZ");
$sfm_captcha->SetCaseInsensitiveMatch(true);
$sfm_captcha->SetNChars(6);
$sfm_captcha->SetNLines(4);
$sfm_captcha->SetFontFile("images/SFOldRepublicBold.ttf");
$sfm_captcha->SetErrorStrings("Please input the code displayed in the image","The code does not match. Please try again.");
$formmailobj->addModule($sfm_captcha);

$formfiller =  new FM_FormFillerScriptWriter();
$formmailobj->addModule($formfiller);

$formmailobj->AddElementInfo("Name","text");
$formmailobj->AddElementInfo("Email","text");
$formmailobj->AddElementInfo("Phone","text");
$formmailobj->AddElementInfo("Address","text");
$formmailobj->AddElementInfo("Message","multiline");
$page_renderer =  new FM_FormPageRenderer();
$formmailobj->addModule($page_renderer);

$validator =  new FM_FormValidator();
$validator->addValidation("Name","req","Please fill in Name");
$validator->addValidation("Email","email","The input for Email should be a valid email value");
$validator->addValidation("Email","req","Please fill in Email");
$validator->addValidation("Phone","req","Please fill in Phone");
$validator->addValidation("Phone","numeric","The input for Phone should be a valid numeric value");
$validator->addValidation("Address","req","Please fill in Address");
$validator->addValidation("Message","req","Please fill in Message");
$formmailobj->addModule($validator);

$data_email_sender =  new FM_FormDataSender(sfm_readfile("./templ/dyno_email_subj.txt"),sfm_readfile("./templ/dyno_email_body.txt"),"%Email%");
$data_email_sender->AddToAddr("Dyno Treats<info@dynotreats.com>");
$data_email_sender->AddToAddr("Dyno Treats - Office<dynotreats@gmail.com>");
$formmailobj->addModule($data_email_sender);

$autoresp =  new FM_AutoResponseSender(sfm_readfile("./templ/dyno_resp_subj.txt"),sfm_readfile("./templ/dyno_resp_body.txt"));
$autoresp->SetToVariables("Name","Email");
$formmailobj->addModule($autoresp);

$tupage =  new FM_ThankYouPage(sfm_readfile("./templ/dyno_thank_u.txt"));
$formmailobj->addModule($tupage);

$sfm_captcha->SetValidator($validator);
$formmailobj->ProcessForm();

?>