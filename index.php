<!DOCTYPE html>
<html lang="ru" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <title>Большое домашнее задание</title>
    <link href="css/style.css" rel="stylesheet">
    <script src="js/jquery-1.8.0.release.js" type="text/javascript"></script>
    <script src="js/highcharts.js"></script>
</head>
<body>
<?include_once ($_SERVER['DOCUMENT_ROOT']."/include/event.php")?>
<?
$event = new \BDZ\Event();
$objEvent = $event;
if (!empty($_REQUEST)) {
    if (!empty($_REQUEST["calendar"]) && !empty($_REQUEST["event"]) && !empty($_REQUEST["event_type"])) {
        $eventStatus = $event->eventAdd($_REQUEST["calendar"], $_REQUEST["event"], $_REQUEST["event_type"]);
    } else {
        $eventStatus = array("status" => "error");
    }
}
$arResult = $event->getEvents();
?>

<div class="content" id="main_content" data-content='<?=json_encode($arResult)?>'>

    <div class="title">
        <h2>Визуализатор событий в языках программирования</h2>
    </div>

    <div class="initial_data">

        <div class="introduce">

            <div class="enter">
                <div class="entered_data_title_left">
                    <h3>Данные</h3>
                </div>

                <?if (!empty($eventStatus["status"])):?>
                    <?if ($eventStatus["status"] == "success"):?>
                        <p class="success">Событие успешно добавлено!</p>
                        <?$_REQUEST = array()?>
                    <?else:?>
                        <?=!empty($eventStatus["message"])?$eventStatus["message"]:"Заполните тип события!"?>
                    <?endif;?>
                <?else:?>
                    <p class="empty"></p>
                    <?$_REQUEST = array()?>
                <?endif;?>

                <form name="add_event" id="add_event" method="POST">
                    <div>
                        <div class="field">
                            <label for="date">Выберите дату:</label>
                            <input id="date" class="data_type" type="date" name="calendar" value="<?=!empty($_REQUEST["calendar"])?$_REQUEST["calendar"]:''?>">
                        </div>

                        <div class="field">
                            <label for="event">Событие: </label>
                            <input name="event" id="event" type="text" value="<?=!empty($_REQUEST["event"])?$_REQUEST["event"]:''?>">
                        </div>

                        <div class="field">
                            <label for="event_type">Тип события:</label>
                            <?$arEventType = $event->getEventTypes();?>
                            <select id="event_type" name="event_type">
                                <option disabled <?=empty($_REQUEST["event_type"])? "selected" : ""?>>
                                    Выберите тип события
                                </option>
                                <?foreach ($arEventType as $arType):?>
                                    <option value='<?=$arType["ID"]?>' <?=(!empty($_REQUEST["event_type"]) && $_REQUEST["event_type"] == $arType["ID"])? "selected" : ""?>>
                                        <?=$arType["NAME"]?>
                                    </option>
                                <?endforeach;?>
                            </select>
                        </div>
                    </div>
                    <div class="entered_data_title_left_add">
                        <p><input class="button_submit" type="submit" value="Добавить"></p>
                    </div>
                </form>
            </div>

            <div class="entered_data">

                <div class="entered_data_title_right">
                    <h3>События</h3>
                </div>

                <div class="entered_data_table">
                    <table>
                        <thead>
                            <tr class="table_head">
                                <th>Дата</th>
                                <th>Событие</th>
                                <th>Тип</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?
                        $arAllEvent = $event->eventSelect();
                        ?>
                        <?if ($arAllEvent):?>
                            <?foreach ($arAllEvent as $arEvent):?>
                                <?foreach ($arEvent as $event):?>
                                    <tr class="table_body">
                                        <td><?=$event["DATE"]?></td>
                                        <td><?=$event["NAME"]?></td>
                                        <td><?=$event["EVENT_TYPE"]?></td>
                                    </tr>
                                <?endforeach;?>
                            <?endforeach;?>
                        <?else:?>
                            <tr class="table_body">
                                <td colspan="3" style="padding: 50px 0;">События не найдены</td>
                            </tr>
                        <?endif;?>
                        </tbody>
                    </table>
                </div>

                <div class="entered_data_title_right_button">
                    <div class="button_clear" data-event="<?=$arAllEvent?>">
                        <p>Очистить события</p>
                    </div>
                </div>
            </div>

        </div>

    </div>


    <div class="schedule">

        <div class="schedule_title">
            <h3>График</h3>
        </div>

        <div class="schedule_svg">
            <div id="container"></div>
            <script src="js/script.js"></script>
        </div>
    </div>

</div>

</body>
</html>