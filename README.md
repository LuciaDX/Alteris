Make non destructive code changes to pmmp on startup<br />
The idea of this is you clone pmmp git repo,<br />
place alteris folder inside (dont forgor to run composer install inside alteris folder) and start the server with Alteris/loader.sh<br />
Bash script will auto fix php exts folder, run main file, pack server into phar and then run it.<br />
Change php installation folder inside bash script!<br />
<br />
You can create 2 folders inside Alteris folder - Include, Overrides<br />
In Include you place all the php files you want to be included inside pmmp root \pocketmine\<br />
In Overrides you create php files where you add changes to code. You can change any php code inside src, vendor and build<br />
<br />
Functions are self explanatory if not figure them out yourself<br />
AddClassMethod, AddClassProperty, AddClassUse, AddClassConst, ReplaceClassMethod, ReplaceClassProperty, ReplaceClassConst<br />
<br />
Also dont judge this shit i dont care
