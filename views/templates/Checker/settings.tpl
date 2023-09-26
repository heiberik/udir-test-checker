
<?php
use oat\tao\helpers\Template;
?>
<link rel="stylesheet" type="text/css" href="<?= Template::css('main.css') ?>" />

<div class="main-container tao-scope udir-check-main-container">
    <div class="udir-check-container"> 

        <h1> Sjekk av skåringsinnstillinger </h1>
        <h2> Overordnet informasjon </h2>
        <table> 
            <tr>
                <th>Oppgaver totalt</th>
                <td><?=(get_data('totalNumberOfTasks'))?></td>
            </tr>
            <tr>
                <th>Oppgaver uten maxscore</th>
                <td><?=(get_data('tasksWithoutMaxscore'))?></td>
            </tr>
            <tr>
                <th>Oppgaver med maxscore over 1</th>
                <td><?=(get_data('tasksWithOver1Maxscore'))?></td>
            </tr>
        </table>
        <h2> Detaljert oversikt oppgaver </h2>
        <div> 
            <?php foreach(get_data('itemArray') as $item):?>
				<div class="checker-item"> 

                    <h3>
                        <?= $item['label'] ?>
                    </h3>
                    <div> Maxscore: <?= $item['maxScore'] ?> </div>
                    <div> Ressurs: <?= $item['uri'] ?> </div>
                    <div class="rp-string" contents='<?= $item['responceProcessing'] ?>'> Responsprosessering: </div>
                    
                
                </div>
			<?php endforeach?>
        </div>
    </div>
</div>