<form action="{$current}&token={$token}" method="post" class="defaultForm form-horizontal">
    <div class="panel">
        {$content}
        <div class="panel-footer">
            <button type="submit" value="1" name="submitWtsResources" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {$this->l('Save')}
            </button>
        </div>
    </div>
</form>
