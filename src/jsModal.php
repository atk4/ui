<?php

namespace atk4\ui;

/**
 * This class generates action, that will be able to loop-back to the callback method.
 */
class jsModal extends jsExpression
{
    public function __construct($title, $url)
    {
        if ($url instanceof VirtualPage) {
            $url = $url->getURL('cut');
        }

        $content = '
  <i class="close icon"></i>
  <div class="header">
    '.htmlspecialchars($title).'
  </div>
  <div class="image content atk-dialog-content">
  <div class="ui active inverted dimmer">
    <div class="ui text loader">Loading</div>
  </div>


  </div>
';

        parent::__construct('
        var m=$("<div>").appendTo("body").addClass("ui scrolling modal").html([content]);
        m.modal({onHide: function() { m.children().remove(); return true; }}).modal("show").find(".content").load([url], function() { m.modal("refresh"); });
        m.find(".atk-dialog-content").data("opener", this).on("close", function() {
            m.modal("hide");
            m.remove();
        });
',
            ['content'=>$content, 'url'=>$url]);
    }
}
