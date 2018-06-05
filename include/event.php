<?php

namespace BDZ;


class Event
{
    public static $BD;
    public static $host = "localhost";
    public static $user = "root";
    public static $pass = "root";
    public static $db_name = "EventViewer";
    private $arEventsData;
    private $arEventTypeData;

    function __construct() {
        self::$BD = new \mysqli(self::$host, self::$user, self::$pass, self::$db_name);
        self::$BD->set_charset('utf8');
        $this->getEvent();
        $this->getEventTypes();
    }

    public function getEvent() {
        $rsEvent = self::$BD -> query("SELECT * FROM Events");
        if ($rsEvent) {
            $this->arEventsData = $rsEvent->fetch_all(MYSQLI_ASSOC);
            return $this->arEventsData;
        }
    }

    public function getEventTypes() {
        $rsTypeEvent = self::$BD -> query("SELECT * FROM EventType ORDER BY `NAME`");
        if ($rsTypeEvent) {
            $this->arEventTypeData = $rsTypeEvent->fetch_all(MYSQLI_ASSOC);
            return $this->arEventTypeData;
        }
    }

    public function eventAdd($data, $event, $event_type)
    {
        if (!empty($data) && !empty(trim($event)) && !empty($event_type)) {
            $eventDate = new \DateTime($data);
            $dateStart =  new \DateTime("29.06.2001");
            $dateEnd =  new \DateTime("07.05.2018");
            $dateE = $eventDate->format("d.m.Y");
            if ($eventDate < $dateEnd && $eventDate > $dateStart) {
                $row = self::$BD -> query("SELECT * FROM Events WHERE `NAME`='".$event."' AND `DATE`='".$dateE."' AND `EVENT_TYPE`=".$event_type."")->fetch_all(MYSQLI_ASSOC);
                if (empty($row)) {
                    $insert = self::$BD -> query("INSERT INTO Events (`NAME`,`DATE`,`EVENT_TYPE`) VALUES ('".$event."','".$dateE."',".$event_type.")");
                    if ($insert) {
                        $this->getEvent();
                        $this->getEventTypes();
                        $this->getEvents();
                        $status = "success";
                        $message = "";
                    } else {
                        $status = "error";
                        $message = "Вы ввели не все данные!";
                    }
                }
            } else {
                $status = "error";
                $message = "Дата выходит за пределы графика";
            }
        } else {
            $status = "error";
            $message = "Вы ввели не все данные!";
        }

        return array(
            "status" => !empty($status) ? $status : "",
            "message" => !empty($message) ? $message : ""
        );
    }

    public function eventSelect()
    {
        if (!empty($this->arEventsData) && !empty($this->arEventTypeData)) {
            foreach ($this->arEventsData as $arEvent) {
                foreach ($this->arEventTypeData as $arEventType) {
                    if ($arEvent["EVENT_TYPE"] == $arEventType["ID"]) {
                        $arEvent["EVENT_TYPE"] = $arEventType["NAME"];
                        $arResult[$arEvent["DATE"]][] = $arEvent;
                    }
                }
            }

            if (!empty($arResult)) {
                krsort($arResult, SORT_NATURAL);
                return $arResult;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getEvents()
    {
        $arEventSelect = $this->eventSelect();

        if (!empty($this->arEventsData) && !empty($this->arEventTypeData)) {
            foreach ($this->arEventTypeData as $arEventType) {
                $arResult[$arEventType["ID"]] = array(
                    'type' => 'column',
                    'name' => $arEventType["NAME"],
                    'color' => $arEventType["COLOR"]
                );
                foreach ($this->arEventsData as $arEvent) {
                    if ($arEvent["EVENT_TYPE"] == $arEventType["ID"]) {
                        $arEventDate = $arEventSelect[$arEvent["DATE"]];
                        $strEvents = "";
                        $arEventsTypeName = [];
                        if (!empty($arEventDate)) {
                            if (count($arEventDate) > 1) {
                                foreach ($arEventDate as $eventDate) {
                                    $arEventsTypeName[$eventDate["EVENT_TYPE"]][] = $eventDate["NAME"];
                                }
                                foreach ($arEventsTypeName as $event => $eventsTypeName) {
                                    $strEvents .= $event . ":<br/>" . implode("<br/>", $eventsTypeName) . "<br/>";
                                }
                                $eventDate = new \DateTime($arEvent["DATE"]);
                                $dateFormat = $eventDate->format("U");
                                $date = (intval($dateFormat) + 14400) * 1000;
                                $arResult[$arEventType["ID"]]["data"][] = [
                                    $date, 27.000, $strEvents
                                ];
                            } else {
                                $eventDate = new \DateTime($arEvent["DATE"]);
                                $dateFormat = $eventDate->format("U");
                                $date = (intval($dateFormat) + 14400) * 1000;
                                $strEvents = $arEventType["NAME"] . ":<br/>" . $arEvent["NAME"] . "<br/>";
                                $arResult[$arEventType["ID"]]["data"][] = [
                                    $date, 27.000, $strEvents
                                ];
                            }
                        }
                    }
                }
                if (empty($arResult[$arEventType["ID"]]["data"])) {
                    unset($arResult[$arEventType["ID"]]);
                }
            }
        }

        if (!empty($arResult)) {
            return $arResult;
        } else {
            return false;
        }
    }

    public function eventClearAll()
    {
        self::$BD -> query("DELETE FROM Events");
        return self::$BD -> affected_rows;
    }
}