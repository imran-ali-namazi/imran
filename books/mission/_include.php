<?php
if (getPageParameterAt() == 'read' && getQueryParameter(VARQueryContent))
	addStyle('print', assetManager::core);
