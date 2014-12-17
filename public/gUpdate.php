<?PHP
/***************************************************************
 * Rich@RichieBartlett.com
 * Copyright(c) 2013, ZyraTech.com.
 * licensing@ZyraTech.com
 * http://www.zyratech.com/EULA.html

//   Updater script to force the website to pull the latest source from GitHub!
//   This script simply executes the /home/rbartlett/updateWWWfromGithub.sh bash script and outputs the console to the webpage...

 ***************************************************************/

if (count($_REQUEST) > 2){
    header("Location: /");
    die();
}//possible hack attempt


//echo "$zf2Path <BR>";
//var_dump( is_dir($zf2Path) );
?>
<html lang="en">
<head>
	<meta charset="utf-8">
</head>
<body>

<div style="float:left;"><a href="/" style="border:0px;"><img src="/images/logo.png" width="178" height="37"  alt=""></a></div>
<div style="clear:both;"></div>

<BR>
<H2>updateWWWfromGithub.sh</H2>
<BR><BR><HR><PRE>
<?PHP


chdir('/var/www/LinkAide/');

$CLIoutput = shell_exec('./updateWWWfromGithub.sh >&1');

echo "$CLIoutput";
?>
</PRE>
            <FOOTER>


                <div style="float:right;padding:0 15px 0 0;">
                    <a href="" style="color:#000000">Link Aide</a>&nbsp;|&nbsp;<a href="" style="color:#000000">Privacy</a>&nbsp;|&nbsp;<a href="" style="color:#000000">Terms</a>&nbsp;|&nbsp;<a href="" style="color:#000000">Help</a>
                </div>

                <div style="clear:both;"></div>

                <HR>

                <!--
                            <P style="color:#DDDDDD">(Need to update links and shortcuts in this FOOTER...)<BR>
                
                                  <table class="LAfootTable">
                                        <tr>
                                              <td>Getting Started:
                                                    <UL>
                                                          <LI>Create a free account</LI>
                                                          <LI>Search idea or project</LI>
                                                    </UL>
                                              </td>
                                              <td>Create:
                                                    <UL>
                                                          <LI>Post your Idea</LI>
                                                          <LI>Start your Project</LI>
                                                          <LI>Create your team</LI>
                                                    </UL>
                                              </td>
                                              <td>Support:
                                                    <UL>
                                                          <LI>Forum</LI>
                                                          <LI>FAQ</LI>
                                                          <LI>Contact Us</LI>
                                                    </UL>
                                              </td>
                                              <td>Company:
                                                    <UL>
                                                          <LI>About LinkAide</LI>
                                                          <LI>Founders</LI>
                                                          <LI>Investors</LI>
                                                          <LI>Partners</LI>
                                                          <LI>Press Releases</LI>
                                                          <LI>Legal</LI>
                                                    </UL>
                                              </td>
                                              <td>Social:
                                                    <UL>
                                                          <LI>Facebook</LI>
                                                          <LI>LinkedIn</LI>
                                                          <LI>Twitter</LI>
                                                          <LI>Vimeo</LI>
                                                          <LI>Google+</LI>
                                                    </UL>
                                              </td>
                                        </tr>
                                  </table>
                            </P>
                
                
                      <HR width="100%">
                
                -->	
                <P style="text-align:right;"><SPAN style="text-align:left; float:left;">&copy; CopyRight 2012 &mdash; <?php echo(date("Y")); ?> by LinkAide.com </SPAN><!-- Designed & written by Richie Bartlett, Jr. -->
                    <?php echo $this->translate('All rights reserved.'); ?></P>
            </FOOTER>

        </div> <!-- /container -->
    </body>
</html>
