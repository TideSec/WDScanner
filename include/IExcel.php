<?php
/**
 * @desc excel接口
 * @author mengdejun
 */
interface  IExcel
{
        //导入excel
        public function import($fileName,$convert_callback_function=null);
        //导出excel
        public function export($fileName="excel");
        //添加行
        public function addRow(array $array,$sheet="sheet1");
        //添加表头
        public function addHead(array $array,$sheet="sheet1");
        //添加工作簿
        public function  addSheet($sheet);
        //释放资源
        public function release();
}
?>