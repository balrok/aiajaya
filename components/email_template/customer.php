<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xml:lang="de" lang="de">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<meta name="format-detection" content="telephone=no" />

	<title><?php echo $title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<div style="background:#F6F6F6; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
<?php // whole body table ?>
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td align="center" valign="top" style="padding:20px 0 20px 0">
			<table bgcolor="#FFFFFF" cellspacing="0" cellpadding="10" border="0" width="800" style="border:1px solid #E0E0E0;">
				<?php // header ?>
				<tr>
					<td valign="top">
						<center>
							<a href="https://balance-dresden.info" style="color:blue;"><img src="<?=
							Yii::app()->getController()->createAbsoluteUrl('/')?>/bilder/logo_email.png" /></a>
						</center>
					</td>
				</tr>
				<?php // body ?>
				<tr>
					<td valign="top" align="center">
						<center>
							<div style="width:775px;text-align:left;">
								<?php echo $msg; ?>
							</div>
						</center>
					</td>
				</tr>
				<?php // footer ?>
				<tr>
					<td bgcolor="#EAEAEA" align="center" style="background:#EAEAEA; text-align:center;"><center><p style="font-size:12px; margin:0;">
						<strong style="color:#006599;">Mit freundlichen Grüßen</strong>	<br />
						Katarina Heidenreich
					</p></center></td>
				</tr>
				<tr>
					<td>
						<center>
							<div style="width:800px;text-align:left;">
								<?php // footer left and right ?>
								<table cellspacing="0" cellpadding="0" border="0" width="800">
									<thead>
									<tr>
										<th align="left" width="325" bgcolor="#EAEAEA" style="font-size:13px; padding:5px 9px 6px 9px; line-height:1em;">&nbsp;</th>
										<th width="10"></th>
										<th align="left" width="325" bgcolor="#EAEAEA" style="font-size:13px; padding:5px 9px 6px 9px; line-height:1em;">&nbsp;</th>
									</tr>
									</thead>
									<tbody>
									<tr>
										<td valign="top" style="font-size:12px; padding:7px 9px 9px 9px; border-left:1px solid #EAEAEA; border-bottom:1px solid #EAEAEA; border-right:1px solid #EAEAEA;">
											<div style="margin-bottom: 15px; font-size: 10pt; color: gray; font-family: 'Arial','sans-serif';">
												<strong style="color:#006599;">Balance Zentrum für Energie- und Körperarbeit</strong><br />
												Hüblerstraße 17<br />
												01309 Dresden<br />
											</div>
										</td>
										<td>&nbsp;</td>
										<td valign="top" style="font-size:12px; padding:7px 9px 9px 9px; border-left:1px solid #EAEAEA; border-bottom:1px solid #EAEAEA; border-right:1px solid #EAEAEA;">
											<div style="margin-bottom: 15px; font-size: 10pt; color: gray; font-family: 'Arial','sans-serif';">
												Telefon: 0351/250 20 440<br/>
												Mobil: 0170/38 36 34 7<br/>
												E-Mail: ddkatarina@hotmail.com<br />
												Internet: <a href="https://balance-dresden.info" style="color:blue;">balance-dresden.info</a><br />
											</div>
										</td>
									</tr>
									</tbody>
								</table>
							</div>
						</center>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</table>
</div>
</body>

</html>
