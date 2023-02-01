<?php
require_once(ROOT.'/components/PHPExcel.php');
require_once(ROOT.'/components/PHPExcel/Writer/Excel2007.php');

class Excel {
    private static function generatorWords($chars, $length, &$words, $prefix = '') {
        if (strlen($prefix) == $length) {
            $words[] = $prefix;
            return;
        }

        for ($i = 0; $i < strlen($chars); $i++) {
            self::generatorWords($chars, $length, $words, $prefix . $chars{$i});
        }

        return;
    }

    public static function setReportPage($studentList, $disciplineList, $passesList, $serviseList, $passesInfo, $groupId, $monthId, $yearId) {
        self::generatorWords('ABCDEFGHIJKLMNOPQRSTUVWXYZ', 1, $letterList);
        self::generatorWords('ABCDEFGHIJKLMNOPQRSTUVWXYZ', 2, $letterList);
        self::generatorWords('ABCDEFGHIJKLMNOPQRSTUVWXYZ', 3, $letterList);

        $xls = new PHPExcel();
        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();

        $dataMatrix = array();

        $dataMatrix[0][0] = 'Номер пары';
        $dataMatrix[1][0] = 'Дисциплина';
        $dataMatrix[2][0] = 'Дата';

        $i = 1;

        foreach($serviseList as $date => $serviceInfo) {
            foreach($serviceInfo as $serviceItem) {
                $dataMatrix[0][$i] = $serviceItem[1];
                $dataMatrix[1][$i] = $disciplineList[$serviceItem[0]];
                $dataMatrix[2][$i]= $date;
                $i++;
            }
        }

        $dataMatrix[0][$i] = 'н/п';
        $dataMatrix[0][$i+1] = 'у/п';
        $dataMatrix[0][$i+2] = 'п';
        $dataMatrix[0][$i+3] = 'Итого';

        $i = 3;
        foreach($studentList as $studentId => $student) {
            $dataMatrix[$i][0] = $student['surname']." ".mb_substr($student['name'], 0, 1).".".mb_substr($student['patronymic'], 0, 1).".";
            $j = 1;
            foreach($serviseList as $serviceInfo) {
                foreach($serviceInfo as $serviceItem) {
                    if($passesList[$studentId][$serviceItem[2]][1][0] == 'н/п' ||
                        $passesList[$studentId][$serviceItem[2]][1][0] == 'у/п' ||
                        $passesList[$studentId][$serviceItem[2]][1][0] == 'п') {
                        $dataMatrix[$i][$j] = $passesList[$studentId][$serviceItem[2]][1][0];
                    } else {
                        $dataMatrix[$i][$j] = "";
                    }
                    $j++;
                }
            }
            $dataMatrix[$i][$j] = $passesInfo[$studentId][0] ? $passesInfo[$studentId][0] : 0;
            $dataMatrix[$i][$j+1] = $passesInfo[$studentId][1] ? $passesInfo[$studentId][1] : 0;
            $dataMatrix[$i][$j+2] = $passesInfo[$studentId][2] ? $passesInfo[$studentId][2] : 0;
            $dataMatrix[$i][$j+3] = $passesInfo[$studentId][0] + $passesInfo[$studentId][1] + $passesInfo[$studentId][2];
            $i++;
        }

        for($i = 1; $i <= count($dataMatrix); $i++) {
            for($j = 1; $j <= count($dataMatrix[$i-1]); $j++) {
                 $sheet->getStyle($letterList[$j-1].$i)->getAlignment()->setWrapText(true);

                 $sheet->getStyle($letterList[$j-1].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                 $sheet->getStyle($letterList[$j-1].$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                 if($i == 2) {
                    $sheet->getStyle($letterList[$j-1].$i)->getAlignment()->setTextRotation(90);
                 }

                 $border = array(
                    'borders'=>array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('rgb' => '000000')
                        )
                    )
                 );
                 $sheet->getStyle($letterList[$j-1].$i)->applyFromArray($border);

                 if($i == 3 && $dataMatrix[$i-1][$j-1] != 'Дата') {
                    $sheet->mergeCells($letterList[$j-1].$i.":".$letterList[$j-2+count($serviseList[$dataMatrix[2][$j-1]])].$i);
                    $sheet->setCellValue($letterList[$j-1].$i, $dataMatrix[$i-1][$j-1]);
                    $j += count($serviseList[$dataMatrix[2][$j-1]])-1;
                 } else if($i == 1 && ($dataMatrix[$i-1][$j-1] == 'н/п' || $dataMatrix[$i-1][$j-1] == 'у/п' || $dataMatrix[$i-1][$j-1] == 'п' || $dataMatrix[$i-1][$j-1] == 'Итого')) {
                    $sheet->mergeCells($letterList[$j-1].$i.":".$letterList[$j-1].($i + 2));
                    $sheet->getStyle($letterList[$j-1].($i+1))->applyFromArray($border);
                    $sheet->getStyle($letterList[$j-1].($i+2))->applyFromArray($border);
                    $sheet->setCellValue($letterList[$j-1].$i, $dataMatrix[$i-1][$j-1]);
                 } else {
                    $sheet->setCellValue($letterList[$j-1].$i, $dataMatrix[$i-1][$j-1]);
                 }
            }
        }

        $objWriter = new PHPExcel_Writer_Excel2007($xls);
        $objWriter->save(ROOT . '/static/xls/рапортичка_'.$groupId.'_'.$monthId.'_'.$yearId.'.xlsx');
    }

    public static function setProgressPage($studentList, $passesInfo, $serviceInfo, $resultsList, $totalInfoList, $averageResult, $groupId, $disciplineId, $yearId) {
        self::generatorWords('ABCDEFGHIJKLMNOPQRSTUVWXYZ', 1, $letterList);
        self::generatorWords('ABCDEFGHIJKLMNOPQRSTUVWXYZ', 2, $letterList);
        self::generatorWords('ABCDEFGHIJKLMNOPQRSTUVWXYZ', 3, $letterList);

        $xls = new PHPExcel();
        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();

        $dataMatrix = array();

        $dataMatrix[0][0] = "ФИО";
        $dataMatrix[0][1] = "у/п";
        $dataMatrix[0][2] = "н/п";
        $dataMatrix[0][3] = "п";
        $dataMatrix[0][4] = "ср/б";
        $dataMatrix[1][0] = "ФИО";
        $dataMatrix[1][1] = "у/п";
        $dataMatrix[1][2] = "н/п";
        $dataMatrix[1][3] = "п";
        $dataMatrix[1][4] = "ср/б";
        $dataMatrix[2][0] = "ФИО";
        $dataMatrix[2][1] = "у/п";
        $dataMatrix[2][2] = "н/п";
        $dataMatrix[2][3] = "п";
        $dataMatrix[2][4] = "ср/б";

        $i = 5;
        foreach($serviceInfo as $serviceItem) {
            if($serviceItem['type'] == 'otr') {
                $dataMatrix[0][$i] = $serviceItem['date'];
                $dataMatrix[1][$i] = 'С отработкой';
                $dataMatrix[2][$i] = $serviceItem['theme'];
                $dataMatrix[0][$i+1] = $serviceItem['date'];
                $dataMatrix[1][$i+1] = 'С отработкой';
                $dataMatrix[2][$i+1] = $serviceItem['theme'];
                $i += 2;
            } else {
                $dataMatrix[0][$i] = $serviceItem['date'];
                $dataMatrix[1][$i] = 'Обычный';
                $dataMatrix[2][$i] = $serviceItem['theme'];
                $i++;
            }
        }
        $dataMatrix[0][$i] = "Аттестация";
        $dataMatrix[0][$i+1] = "Семестр";
        $dataMatrix[0][$i+2] = "Экзамен";
        $dataMatrix[0][$i+3] = "В зачётку";

        $i = 3;
        foreach($studentList as $studentId => $student) {
            $dataMatrix[$i][0] = $student['surname']." ".mb_substr($student['name'], 0, 1).".".mb_substr($student['patronymic'], 0, 1).".";
            $dataMatrix[$i][1] = $passesInfo[$studentId][0];
            $dataMatrix[$i][2] = $passesInfo[$studentId][1];
            $dataMatrix[$i][3] = $passesInfo[$studentId][2];
            $dataMatrix[$i][4] = $averageResult[$studentId];
            $j = 5;
            foreach($serviceInfo as $lessonId => $serviceItem) {
                if($dataMatrix[1][$j] == 'С отработкой') {
                    $dataMatrix[$i][$j] = $resultsList[$studentId] ? $resultsList[$studentId][$lessonId][1][0] : "";
                    $dataMatrix[$i][$j+1] = $resultsList[$studentId] ? $resultsList[$studentId][$lessonId][1][1] : "";
                    $j += 2;
                } else {
                    $dataMatrix[$i][$j] = $resultsList[$studentId] ? $resultsList[$studentId][$lessonId][1][0] : "";
                    $j++;
                }
            }
            $dataMatrix[$i][$j] = $totalInfoList[$studentId]['attestation'];
            $dataMatrix[$i][$j+1] = $totalInfoList[$studentId]['semester'];
            $dataMatrix[$i][$j+2] = $totalInfoList[$studentId]['exam'];
            $dataMatrix[$i][$j+3] = $totalInfoList[$studentId]['total'];
            $i++;
        }

        echo "<pre>";
        print_r($dataMatrix);
        echo "</pre>";

        for($i = 1; $i <= count($dataMatrix); $i++) {
            for($j = 1; $j <= count($dataMatrix[$i-1]); $j++) {
                $sheet->getStyle($letterList[$j-1].$i)->getAlignment()->setWrapText(true);

                $sheet->getStyle($letterList[$j-1].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($letterList[$j-1].$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                if($dataMatrix[$i-1][$j-1] === 'Аттестация' || $dataMatrix[$i-1][$j-1] === 'Семестр' ||
                $dataMatrix[$i-1][$j-1] === 'Экзамен' || $dataMatrix[$i-1][$j-1] === 'В зачётку') {
                    $sheet->getStyle($letterList[$j-1].$i)->getAlignment()->setTextRotation(90);
                }

                $sheet->setCellValue($letterList[$j-1].$i, $dataMatrix[$i-1][$j-1]);

                $border = array(
                    'borders'=>array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('rgb' => '000000')
                        )
                    )
                );
                $sheet->getStyle($letterList[$j-1].$i)->applyFromArray($border);

                if($dataMatrix[0][$j-1] == 'ФИО' || $dataMatrix[0][$j-1] == 'н/п' ||
                $dataMatrix[0][$j-1] == 'у/п' || $dataMatrix[0][$j-1] == 'п' || $dataMatrix[0][$j-1] == 'Аттестация' ||
                $dataMatrix[0][$j-1] == 'Семестр' || $dataMatrix[0][$j-1] == 'Экзамен' ||
                $dataMatrix[0][$j-1] == 'В зачётку' || $dataMatrix[0][$j-1] == 'ср/б') {
                    $sheet->mergeCells($letterList[$j-1].'1'.":".$letterList[$j-1].'3');
                    $sheet->getStyle($letterList[$j-1].'2')->applyFromArray($border);
                    $sheet->getStyle($letterList[$j-1].'3')->applyFromArray($border);
                    $sheet->setCellValue($letterList[$j-1].'1', $dataMatrix[0][$j-1]);
                }

                if($i < 4 && $dataMatrix[1][$j-1] == 'С отработкой') {
                    $sheet->mergeCells($letterList[$j-1].$i.":".$letterList[$j].$i);
                    $j++;
                }

                $sheet->setCellValue($letterList[$j-1].$i, $dataMatrix[$i-1][$j-1]);
            }
        }

        $objWriter = new PHPExcel_Writer_Excel2007($xls);
        $objWriter->save(ROOT . '/static/xls/успеваемость_'.$groupId.'_'.$disciplineId.'_'.$yearId.'.xlsx');
    }

    public static function setSummaryPage($studentList, $disciplineList, $summaryInfo, $passesInfo, $groupId, $yearId) {
        self::generatorWords('ABCDEFGHIJKLMNOPQRSTUVWXYZ', 1, $letterList);
        self::generatorWords('ABCDEFGHIJKLMNOPQRSTUVWXYZ', 2, $letterList);
        self::generatorWords('ABCDEFGHIJKLMNOPQRSTUVWXYZ', 3, $letterList);

        $xls = new PHPExcel();
        $xls->setActiveSheetIndex(0);
        $sheet = $xls->getActiveSheet();

       $dataMatrix = array();

       $dataMatrix[0][0] = 'Дисциплина';

       $i = 1;
       foreach($summaryInfo as $disciplineId => $discipline) {
            $dataMatrix[0][$i] = $disciplineList[$disciplineId];
            $i++;
       }

       $dataMatrix[0][$i] = 'н/п';
       $dataMatrix[0][$i+1] = 'у/п';
       $dataMatrix[0][$i+2] = 'п';
       $dataMatrix[0][$i+3] = 'итого';

       $i = 1;
       foreach($studentList as $studentId => $student) {
           $dataMatrix[$i][0] = $student['surname']." ".mb_substr($student['name'], 0, 1).".".mb_substr($student['patronymic'], 0, 1).".";
           $j = 1;
           foreach($summaryInfo as $disciplineId => $summary) {
               $dataMatrix[$i][$j] = $summaryInfo[$disciplineId][$studentId];
               echo "<pre>";
               echo $disciplineList[$disciplineId];
               print_r($dataMatrix[$i][$j]);
               echo "</pre>";
               $j++;
           }
           $dataMatrix[$i][$j] = $passesInfo[$studentId][0];
           $dataMatrix[$i][$j+1] = $passesInfo[$studentId][1];
           $dataMatrix[$i][$j+2] = $passesInfo[$studentId][2];
           $dataMatrix[$i][$j+3] = $passesInfo[$studentId][0] + $passesInfo[$studentId][1] + $passesInfo[$studentId][2];
           $i++;
       }

       for ($i = 1; $i <= count($dataMatrix); $i++) {
        	for ($j = 1; $j <= count($dataMatrix[$i-1]); $j++) {
        	    $sheet->getStyle($letterList[$j-1].$i)->getAlignment()->setWrapText(true);

        	    $sheet->getStyle($letterList[$j-1].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        	    $sheet->getStyle($letterList[$j-1].$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        	    if($i == 1 && $dataMatrix[$i-1][$j-1] != 'н/п' && $dataMatrix[$i-1][$j-1] != 'у/п'
        	    && $dataMatrix[$i-1][$j-1] != 'п' && $dataMatrix[$i-1][$j-1] != 'Итого') {
        	        $sheet->getStyle($letterList[$j-1].$i)->getAlignment()->setTextRotation(90);
        	    }

        	    $border = array(
                	'borders'=>array(
                		'allborders' => array(
                			'style' => PHPExcel_Style_Border::BORDER_THIN,
                			'color' => array('rgb' => '000000')
                		)
                	)
                );
                $sheet->getStyle($letterList[$j-1].$i)->applyFromArray($border);

        	    if(is_array($dataMatrix[$i-1][$j-1]) && is_numeric($dataMatrix[$i-1][$j-1][0])) {
        	        $sheet->setCellValue($letterList[$j-1].$i, $dataMatrix[$i-1][$j-1][0]);
        	    } else {
        	        $sheet->setCellValue($letterList[$j-1].$i, $dataMatrix[$i-1][$j-1]);
        	    }
        	}
       }

       $objWriter = new PHPExcel_Writer_Excel2007($xls);
       $objWriter->save(ROOT . '/static/xls/сводный_лист_'.$groupId.'_'.$yearId.'.xlsx');
    }
}

function cmp($studentA, $studentB) {
    return strcasecmp($studentA['surname'], $studentB['surname']);
}