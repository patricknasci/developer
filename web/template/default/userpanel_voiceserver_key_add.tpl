<div class="row-fluid">
    <div class="span12">
        <ul class="breadcrumb">
            <li><a href="userpanel.php">Home</a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=vo"><?php echo $gsprache->voiceserver;?></a> <span class="divider">/</span></li>
            <li><a href="userpanel.php?w=vo&amp;d=pk&amp;id=<?php echo $id;?>">Token</a> <span class="divider">/</span></li>
            <li><?php echo $gsprache->add;?> <span class="divider">/</span></li>
            <li class="active"><?php echo $address;?></li>
        </ul>
    </div>
</div>
<div class="row-fluid">
    <div class="span6">
        <form class="form-horizontal" action="userpanel.php?w=vo&amp;d=pk&amp;id=<?php echo $id;?>&amp;r=vo" onsubmit="return confirm('<?php echo $gsprache->sure;?>');" method="post">
            <div class="control-group">
                <label class="control-label" for="groupname"><?php echo $sprache->groupname;?></label>
                <div class="controls">
                    <select id="groupname" name="group">
                        <?php foreach ($servergroups as $id => $name) { ?>
                        <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEdit"></label>
                <div class="controls">
                    <button class="btn btn-primary pull-right" id="inputEdit" type="submit"><i class="icon-plus-sign icon-white"></i></button>
                    <input type="hidden" name="action" value="ad">
                </div>
            </div>
        </form>
    </div>
</div>