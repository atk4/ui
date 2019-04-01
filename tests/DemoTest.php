<?php

namespace atk4\ui\tests;

/**
 * Making sure demo pages don't throw exceptions and coverage is
 * handled.
 */
class DemoTest extends \atk4\core\PHPUnit_AgileTestCase
{
    public function setUp()
    {
        chdir('demos');
    }

    public function tearDown()
    {
        chdir('..');
    }

    public function inc($f)
    {
        $_SERVER['REQUEST_URI'] = '/ui/'.$f;
        include $f;

        return $app;
    }

    private $regex = '/^..DOCTYPE/';

    /**
     * @dataProvider demoList
     */
    public function testDemo($page)
    {
        $this->expectOutputRegex($this->regex);

        try {
            $this->inc($page)->run();
        } catch (\atk4\core\Exception $e) {
            $e->addMoreInfo('test', $page);

            throw $e;
        }
    }

    public function demoList()
    {
        $copy_paste = trim('
  accordion.php
  accordion-nested.php
  autocomplete.php
  breadcrumb.php
  button.php
  card.php
  checkbox.php
  columns.php
  console.php
  crud.php
  crud2.php
  crud3.php
  dropdown-plus.php
  field.php
  field2.php
  form.php
  form2.php
  form3.php
  form4.php
  form5.php
  form6.php
  form-custom-layout.php
  form-section.php
  form-section-accordion.php
  grid.php
  grid-layout.php
  header.php
  index.php
  init.php
  js.php
  jscondform.php
  menu.php
  message.php
  modal.php
  multitable.php
  notify.php
  notify2.php
  paginator.php
  progress.php
  recursive.php
  reloading.php
  scroll-container.php
  scroll-grid.php
  scroll-grid-container.php
  scroll-lister.php
  scroll-table.php
  sse.php
  sticky.php
  sticky2.php
  table.php
  table2.php
  tablecolumnmenu.php
  tabs.php
  toast.php
  upload.php
  view.php
  virtual.php
  vue-component.php
  wizard.php
');
        /* DO NOT WORK
        jssearch.php.
        jssortable.php
        label.php
        layouts.php
        layouts_admin.php
        layouts_error.php
        layouts_manual.php
        layouts_nolayout.php
        lister.php
        lister-ipp.php
        loader2.php
        loader.php
        modal2.php
        popup.php
        tablefilter.php
        */
        $copy_paste = explode("\n", $copy_paste);
        $copy_paste = array_map(function ($i) {
            return [trim($i)];
        }, $copy_paste);

        return $copy_paste;
    }

    public function testLayout()
    {
        $this->expectOutputRegex($this->regex);
        include 'layouts_manual.php';
    }
}
