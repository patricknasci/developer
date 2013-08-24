<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="admin.php">Home</a> <span class="divider">/</span></li>
            <li><a href="admin.php?w=ad"><?php echo $sprache->heading_addons;?></a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->del;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $menudescription;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span11"><?php echo $sprache->sure;?></div>
</div>
<div class="row-fluid">
    <div class="span11"><?php echo $sprache->sure2;?></div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="admin.php?w=ad&amp;d=dl&amp;id=<?php echo $addonid;?>&amp;r=ad" onsubmit="return confirm('<?php echo $gsprache->sure; ?>');" method="post">
            <input type="hidden" name="token" value="<?php echo token();?>">
            <input type="hidden" name="action" value="dl">
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary pull-right" id="inputEdit" type="submit"><i class="icon-edit icon-white"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>