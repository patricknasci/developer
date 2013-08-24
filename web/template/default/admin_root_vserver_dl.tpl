<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=vs"><?php echo $gsprache->virtual;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->del;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $ip;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span12">
        <dl class="dl-horizontal">
            <dt><?php echo $sprache->ip;?>:</dt>
            <dd><?php echo $ip;?></dd>
            <dt><?php echo $sprache->description?>:</dt>
            <dd><?php echo $description." ".$bitversion;?> Bit</dd>
        </dl>
    </div>
</div>
<div class="row-fluid">
    <div class="span8">
        <form class="form-horizontal" action="admin.php?w=vs&amp;d=dl&amp;id=<?php echo $id;?>&amp;r=vs" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="dl">
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary pull-right" id="inputEdit" type="submit"><i class="icon-remove-sign icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>