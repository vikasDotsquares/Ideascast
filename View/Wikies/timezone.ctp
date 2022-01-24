<html>
    <head></head>
    <body>
        <script language="javascript">
            function getTimeZoneSign() {
                var offset = new Date().getTimezoneOffset(),
                    o = Math.abs(offset);
                return (offset < 0 ? "+" : "-");
                //return (offset < 0 ? "+" : "-") + ("00" + Math.floor(o / 60)).slice(-2) + ":" + ("00" + (o % 60)).slice(-2);
            }
            function getTimeZoneHour() {
                var offset = new Date().getTimezoneOffset(),
                    o = Math.abs(offset);
                return ("00" + Math.floor(o / 60)).slice(-2);
                //return (offset < 0 ? "+" : "-") + ("00" + Math.floor(o / 60)).slice(-2) + ":" + ("00" + (o % 60)).slice(-2);
            }
            function getTimeZoneMin() {
                var offset = new Date().getTimezoneOffset(),
                    o = Math.abs(offset);
                return ("00" + (o % 60)).slice(-2);
                //return (offset < 0 ? "+" : "-") + ("00" + Math.floor(o / 60)).slice(-2) + ":" + ("00" + (o % 60)).slice(-2);
            }
            
            ourDate = new Date();
            document.write("The time and date at your computer's location is: "
                    + ourDate.toLocaleString()
                    + ".<br/>");
            document.write("The time zone offset between local time and GMT is "
                    + ourDate.getTimezoneOffset()
                    + " minutes.<br/>");
            document.write("The time and date (GMT) is: "
                    + ourDate.toGMTString()
                    + ".<br/>");
            
            

        </script>
        <?php 
        $minutes = '<script type="text/javascript" >document.write(ourDate.getTimezoneOffset())</script>';
        $sign =  '<script type="text/javascript" >document.write(getTimeZoneSign())</script>';
        $hour =  '<script type="text/javascript" >document.write(getTimeZoneHour())</script>';
        $min =  '<script type="text/javascript" >document.write(getTimeZoneMin())</script>';
        
        
        
        echo $sign.$hour.':'.$min;
        echo '<br>';
        echo $minutes;
        echo '<br>';
        
        echo '<br>';
        echo date("Y-m-d h:i:s A");
        echo ' Server time <br>';
        echo '<br>';
        
        
        echo '<br>';
        $rfc_1123_date = gmdate('Y-m-d h:i:s', time()); 
        $sertimestamp = strtotime($rfc_1123_date." -540 minute");
        echo date('Y-m-d h:i:s A', $sertimestamp);
        echo '<br>';
        
      
        ?>
    </body>
</html>

<?php 



die; ?>