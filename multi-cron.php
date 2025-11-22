<?php
include "connect.php";
$q = $connection->query("SELECT url FROM shortener WHERE status=1 LIMIT 50");
$urls = [];
while($row = $q->fetch_assoc()){
    $urls[] = $row['url'];
}

$mh = curl_multi_init();
$chs = [];

foreach($urls as $u){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $u);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_multi_add_handle($mh, $ch);
    $chs[] = $ch;
}

$running = null;
do{
    curl_multi_exec($mh, $running);
    curl_multi_select($mh);
}while($running > 0);

foreach($chs as $ch){
    curl_multi_remove_handle($mh, $ch);
}

curl_multi_close($mh);
echo "Cron executed";
?>
        </pre>

        <p>Use this in cron:</p>
        <code>*/1 * * * * php -q /home/USER/public_html/cron_run.php >/dev/null 2>&1</code>
    </div>

</body>
</html>
