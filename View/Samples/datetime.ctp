
<?php
/*

echo $this->Html->script('projects/rewards/rewards', array('inline' => true));
echo $this->Html->css('projects/reward-center');


*/
/*$prjid = 219;
$element_key = $this->TaskCenter->userElementsDemo(1, [$prjid] );//4
foreach ($element_key as $key => $element_id) {
    $element_permit_type = $this->TaskCenter->element_permit_type( $prjid, 1, $element_id );//1
    $element_participants = $this->ViewModel->element_participants($element_id);//1
    $workspace_id = element_workspace($element_id);
    $element_sharer = $this->ViewModel->element_sharer($element_id);
    $workspace_permit_type = $this->TaskCenter->workspace_permit_type( $prjid, 1, $workspace_id );//3
    $creator_detail = $this->Common->userFullName(1);//0.5
    $creator_detail = get_user_data(1);//2
}*/



// echo $this->Html->script('projects/plugins/bs-datetime-picker/bootstrap-datetimepicker', array('inline' => true));
echo $this->Html->script('projects/multi-select', array('inline' => true));
// echo $this->Html->css('projects/bs-datetime-picker/bootstrap-datetimepicker', array('inline' => true));
?>
<?php
$alphabet = array(
   array(
    'a) Practice honestly and with integrity',
    'b) Work within limitations of the role and recognise own level of competence',
    'c) Display an appropriate level of interest and enthusiasm',
    'd. Display a professional image in behaviour and appearance, adhere to local policy and national guidelines on dress code for prevention and control of infection control, including footwear, hair, piercings and nails.',
    'e) Adhere to the placement sickness and absence policy',
    'f) Display a responsible approach to time management including punctuality and reliability.',
    'g) Act in a manner that is non-discriminatory, kind, sensitive and compassionate that values diversity and acts within professional boundaries',
    'h) Engage with people in a way that ensures dignity is maintained whilst adopting an appropriate attitude',
    'i) Demonstrate an understanding of the impact culture, religion, spiritual beliefs, gender and sexuality have on health, illness and disability',
    'k) Display an awareness of how one’s own values, beliefs, emotions, health and well-being impact on practice.'
   ),array(
   'a) Maintain patient confidentiality in written and verbal communications',
'b) Demonstrate effective and appropriate verbal and non-verbal skills in communicating information, advice, instruction and professional opinion to service users, colleagues and others',
'c) Communicate with people in a manner and at a level and pace that is consistent with their abilities and appropriate form of communication',
'd) Select and use appropriate forms of verbal and non-verbal communication with service users and others',
'e) Be aware of the characteristics and consequences of verbal and non-verbal communication and how this can be affected by factors such as age, culture, ethnicity, gender, socio-economic status and spiritual or religious beliefs',
'f) Assist in reducing barriers to effective communication and the environment to meet the needs of the individual',
'g) Recognise the need to use interpersonal skills to encourage the active participation of service users',
'h) Use communication skills in the reception and identification of service users and the transfer of service users to the care of others',
'i) Demonstrate honesty, integrity and respect'
   ),array(
   'a) Maintain patient confidentiality and data protection when handling and completing medical records',
'b) Complete documentation that is neat, legible and accurate',
'c) Correctly checks documentation relating to patient identification and the procedure to be carried out under supervision',
'd) Participates in the WHO checklist and the 5 Steps to Safer Surgery',
'e) Assists in the documentation of patient care according to current legislation, policies and procedure',
'f) Understands the consequences of not completing documentation correctly or falsely and in a timely manner',
'g) Demonstrate appropriate use and sharing of information, as required',
   ),array(
   'a) Promote and apply measures designed to practice in a safe environment that meets the needs of an individual patient',
'b) Consistently demonstrate correct disposal of different types of waste such as, clinical, non-clinical and hazardous waste.',
'c) Assist in the control of the physical environment e.g. temperature, lighting, ventilation and humidity.',
'd) Demonstrate understanding of the need to be competent prior to using any piece of equipment and seek training when necessary from competent trainers.',
'e) Consistently demonstrate safe and suitable moving and handling technique at all times to prevent injury to self, patient and other staff.',
'f) Recognise the need to report problems as they arise, to the appropriate individual',
'g) Be able to establish safe environments for practice, which minimise risks to service users, those treating them and others, include the use of hazard control and particularly infection control',
'h) Demonstrate the safe disposal of sharps, clinical clothing, bedding and waste to minimise cross infection',
'i) Discuss prevention and management of sharps/splash injuries.'
   ),array(
   'a) Apply basic knowledge of anatomy and physiology, and the risks associated with patient positioning',
'b) Assist in the provision of pressure area care, utilising appropriate pressure relieving devices, and documents the care given',
'c) Identify factors that may compromise patient comfort and dignity during procedures and act accordingly.',
'd) Contribute to the assessment of the holistic needs of the patient in the perioperative environment.',
'e) Assist in the assessment of the patients’ skin integrity and risks whilst on the operating table.',
'f) Assist and contribute in the assessment, planning, implementation and evaluation of the holistic needs of the patient.',
'g) Demonstrate the ability to use patient transfer equipment safely and appropriately',
'h) Demonstrate the ability to return the operating table to the required position in the event of an emergency',
'i) Demonstrate the ability to safely position the patient following surgery',
'j) Demonstrate knowledge and understanding and the potential complications when positioning patient for surgical',
'procedures '
   ),array(
'a) Demonstrate the principles of ANTT',
'b) Demonstrate understanding of the 5 moments of hand hygiene',
'c) Demonstrate effective handwashing techniques, including social handwashing, standard antisepsis and surgical antisepsis using the Ayliffe technique',
'd) Demonstrate the ability to maintain a sterile field',
'e) Demonstrate an understanding of the management of standard aseptic, critical and micro-critical aseptic fields and the practitioner’s role and responsibility for maintaining them',
'f) Demonstrate understanding of the importance of informing the surgical team when a sterile field has been compromised',
'g) Demonstrate changing contaminated gloves during clinical procedures',
),array(
'a) Promote and apply measures designed to prevent or control infection',
'b) Consistently demonstrate correct cleaning procedures for decontaminating clinical areas, including minimising contamination from blood-borne/airborne infections e.g. Covid-19, MRSA, C-Difficile, HIV, Hepatitis ABC, vCJD',
'c) Demonstrate an understanding of the sources, routes and transmission of infection by pathological organisms and methods of destruction through decontamination and sterilisation',
'd) Minimise the risk of infection by highlighting patients at risk, managing isolation and preparing the equipment and environment',
'e) Prepare and decontaminate equipment appropriate to the planned procedure',
'f) Assist in the safe storage of sterile packs, instrument trays and other sundries',
'g) Check expiry dates, sterility and integrity of sterile packs, instruments and other sundries before use',
'h) Demonstrate application of single use items and discuss the implications of multiuse',
'i) Comply with local and national guidance in relation to decontamination, tracking and traceability of medical devices',
'j) Consistently apply the principles of standard precautions and select appropriate personal protective equipment and use it correctly',
'k) Take appropriate action if equipment, packs or trays are found to be faulty including decontaminating, locating and obtaining replacements and reporting faults',
'l) Ensure, following surgery, that the patient is clean, dry and free of any Bodily Fluid contamination'
) ,array(
'a) Discuss and apply a working knowledge of the clinical requirements for valid consent.',
'b) Apply the principles of data protection.',
'c) Demonstrate the application of moral, ethical, cultural and spiritual principles to the planning of patient care.',
'd) Recognise, respect and understand the need to report behaviour that undermines equality and diversity.',
'e) Apply equality, diversity and rights in accordance with legislation, polices and relevant standards.',
'f) Act in a way that is in accordance with legislation, policies, procedures and best practice.',
'g) Communicate information only to those people who need to know it consistent with legislation, policies and procedures, (for example Safeguarding and Data protection).',
),array(
'a) Demonstrate the routine/daily checks, preparation of the anaesthetic room, operating theatre and anaesthetic equipment',
'b) Demonstrate the ability to prepare anaesthetic room to provide individualised patient care taking into account ASA scores',
'c) Assist with the routine/daily safety function checks in line with local policy, national legislation and the Association of Anaesthetists (AA):',
'd) Identify location of gas shut off valves and cylinder storage areas',
)


,array(
'a) Demonstrate awareness of situations which compromise patient safety',
'b) Communicate appropriate information to the anaesthetist regarding administration or non-administration of drugs likely to impact on anaesthesia e.g. antibiotics, VTE prophylaxis',
'c) Report to anaesthetist and wider multidisciplinary team any relevant pre-existing medical conditions which may adversely affect the patient during anaesthesia',
'd) Use the WHO safer surgery checklist to systematically identify the proposed site of operation and highlight important aspects/discrepancies to the relevant team members',
'e) Verify patient’s identity and confirm consent with scrub practitioner.',
'f) Communicate any concerns with regard to consent to the appropriate members of the multidisciplinary team.',
'g) Demonstrate the ability to recognise signs of patient anxiety and offer reassurance',
'h) Demonstrate the ability to assist in the care of a patient during a procedure under:(General anaesthesia, Regional Anaesthesia,Peripheral nerve blocks,Sedation,Local Anaesthesia)',
'i) Demonstrate an understanding of the management of a patient with an individual/specific need or allergy'
),array(
'a) Apply tourniquet correctly (select and apply appropriate cuff)',
'b) Monitor tourniquet pressure',
'c) Record tourniquet start and finish time',
'd) Discuss the importance of time limitation',
'e) Regularly update team regarding tourniquet time',
'f) Check Diathermy machine',
'g) Safely apply a diathermy electrode and remove it when no longer required',
'h) Demonstrate the safe use of compression devices, e.g. (Flowtrons) for the prevention of DVT/VTE, or TED stockings',
'i) Safe and effective use of active warming devices e.g. bair hugger',
),array(
'a) Demonstrate the ability to correctly apply routine non-invasive monitoring:',
'b) Understand anaesthetic charts and trends, perform charting of physiological data and report monitoring status appropriately to the anaesthetist/registered practitioner',
'c) Demonstrate understanding of sampling/interpreting blood glucose',
'd) Apply the principles of, and participate in, maintaining normothermia in an intra-operative patient to include: (Fluid warmers,Forced-Air Warming,Regulation of ambient temperature)',
),array(
'a) Demonstrate a basic understanding of the indications of drugs/medications routinely used in anaesthesia, including all of the following:(volatile agents,anaesthetic gases,intravenous induction agents,opioids,sedatives,depolarising and non-depolarising muscle relaxants,reversal agents,local anaesthetic agents,non-steroidal analgesics,non-opioid analgesics ,anti-emetics,antibiotics)',
'b) Verify the identity of the patient before medication is administered',
'c) Demonstrate the ability to maintain clear, accurate records of all medicine administered, intentionally withheld or refused by the patient',
'd) Adheres to approved national guidelines, local and HEI policies for the secure storage and management of medicines, including controlled drugs',
),array(
'a) Demonstrate an awareness of the principles involved in assessing airway for potential difficulty with intubation and/or ventilation',
'b) Identify and report any previous problems with anaesthesia, if applicable',
'c) Demonstrate ability to prepare and check equipment for routine intubation',
'd) Assist the anaesthetist in securing the airway using the following:(Supraglottic airways,Endotracheal tubes,Oropharyngeal (Guedel) and/or nasopharyngeal airway,Face masks,Catheter/angle mount,Filters,Laryngoscopes / blade selection,Bougies and stylets,Oxygen supply and delivery devices,Suction Equipment)',
'e) Assist with the positioning of the patient to maximise patient comfort and provide optimal access for the anaesthetist',
'f) Identify location and contents of difficult intubation trolley, which may include:(Fibre-optic laryngoscope and attachments ,Video Laryngoscope including flexible laryngoscope,Cook’s airway/Aintree catheter,Manujet,Polio blade Laryngoscope,McCoy laryngoscope,Front of Neck Access equipment / Cricothyroidotomy set'
),array(
'a) Demonstrate the correct use of personal protective clothing including but not exclusive to laser and radiation (x-ray) use',
'b) Assist in checking required items with the scrub practitioner to ensure suitability and reduce / prevent waste',
'c) Ensure that required equipment is available and checked fit for use prior to commencement of the list',
'd) Demonstrate how to select, open and prepare gown and gloves for scrub personnel',
'e) Demonstrate the correct method of assisting scrub staff to don their surgical gown',
'f) Demonstrate safe practice when connecting devices from the surgical field including but not exclusive to light leads, camera, diathermy, suction',
'g) Articulate the principles and methods for monitoring the surgical field and discuss the importance of informing a surgical team their sterility is compromised',
'h) Demonstrate passing of sterile items in the correct manner',
'i) Demonstrate awareness of estimated blood loss and implications',
'j) Demonstrate the correct procedure for the checking of swabs, sharps and instruments',
'k) Assist in the preparation for and receiving of clinical specimens from the sterile field in line with hospital policy',
'l) Articulate the specific requirements for handling and transporting different types of histology and microbiology specimens in order that they remain fit for investigation',
'm) Assist with the dispatch of instruments and equipment for decontamination and re-processing'
)


);
 ?>


<div class="row">
    <div class="col-xs-12">
        <div class="row">
            <section class="content-header clearfix">
                <h1 class="pull-left">
                    Samples
                    <p class="text-muted date-time" style="padding:5px 0; margin: 0 !important;">
                        <span style="text-transform: none;">Create & Check your sample pages here</span>
                    </p>
                </h1>
            </section>
        </div>

        <div class="box-content">
            <div class="row ">
                <div class="col-xs-12">
                    <div class="box noborder margin-top">
                        <div class="box-header filters" style="">
                            <!-- Modal Boxes -->
                            <div class="modal modal-success fade" id="modal_large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <div class="modal modal-success fade" id="modal_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <div class="modal modal-success fade" id="modal_small" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xs">
                                    <div class="modal-content"></div>
                                </div>
                            </div>
                            <!-- /.modal -->
                        </div>
                        <div class="box-body clearfix" style="min-height: 800px;" id="box_body">
                            <!-- <input type="text" name="" id="datetime1">
                            <?php pr( $this->Session->read('stoken'));  ?>
                            <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js" integrity="sha256-/H4YS+7aYb9kJ5OKhFYPUjSJdrtV6AeyJOtTkw6X72o=" crossorigin="anonymous"></script>

                            <br><br>
                            <label>encrypted</label>
                            <div id="demo1"></div>
                            <br>

                            <label>decrypted</label>
                            <div id="demo2"></div>

                            <br>
                            <label>Actual Message</label>
                            <div id="demo3"></div>


                            <div id="datepicker"></div> -->
                            <div id="ss" >
                                <img src="../images/Penguins.jpg" class="fadein" height="300">
                                <div class="fadein h">test data</div>
                                <img src="../images/Penguins.jpg" class="fadein" height="220">
                                <div class="fadein h">test data</div>
                                <img src="../images/Penguins.jpg" class="fadein" height="330">
                                <div class="fadein h">test data</div>
                                <img src="../images/Penguins.jpg" class="fadein" height="400">
                                <div class="fadein h">test data</div>
                                <img src="../images/Penguins.jpg" class="fadein" height="100">
                                <div class="fadein h">test data</div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
            </div>
        </div>
    </div>
</div>
<style type="text/css">
.fadein{
    opacity: 0;
}
.fadein:first-child {
    opacity: 1;
}
.fadein.h{
    height: 150px;
    display: block;
    background-color: #ccc;
}
    .ss li {
        min-height: 120px;
        display: block;
        float: left;
        width: 100%;
    }
    .btn.nofill, .open > .btn.nofill {
        background-color: transparent;
        color: #000;
    }
</style>
<?php pr($alphabet); ?>
<script type="text/javascript">
    $(function(){

        var alphabet = '<?php echo json_encode($alphabet); ?>' ;
        console.log(JSON.parse(alphabet))



        $(window).on("load",function() {
          $(window).scroll(function() {
            var windowBottom = $(this).scrollTop() + $(this).innerHeight();
            $(".fadein").each(function() {

              var objectBottom = $(this).offset().top + ($(this).outerHeight()/2);

              if (objectBottom < windowBottom) { //object comes into view (scrolling down)
                if ($(this).css("opacity")==0) {$(this).fadeTo(500,1);}
              } else { //object goes out of view (scrolling up)
                if ($(this).css("opacity")==1) {$(this).fadeTo(500,0);}
              }
            });
          }).scroll(); //invoke scroll-handler on page-load
        });
        /*var lastScrollTop = 0;
        $(window).scroll( function(){
            var st = $(this).scrollTop();
            if (st > lastScrollTop){
                console.log('down');
                $('.fadein').each( function(i){

                    var bottom_of_element = $(this).offset().top + ($(this).outerHeight()/4);
                    var bottom_of_window = $(window).scrollTop() + $(window).height();

                    if( bottom_of_window > bottom_of_element ){
                        $(this).animate({'opacity': 1},1000);
                    }

                });
            }
            else{
                console.log('up');
                $('.fadein').each( function(i){

                    var bottom_of_element = $(this).offset().top + ($(this).outerHeight()/2);
                    var bottom_of_window = $(window).scrollTop() + $(window).height();

                    if( bottom_of_element > bottom_of_window ){
                        $(this).animate({'opacity': 0},1000);
                    }

                });
            }
            lastScrollTop = st;

        });*/
    })
</script>
