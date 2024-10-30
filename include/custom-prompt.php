            <style>
wplrpPromptWrapper{
   position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,.8);
    z-index: 10000000000000;
    display: none;
}               
                wplrpprompt{display:block;}
                wplrppromptcontainer{
                    padding: 20px;
                    background: #fff;
                    box-sizing: border-box;
                    width: 450px;
                    position: fixed;
                    top: 10px;
                    left:calc(50% - 225px);
                    border:1px solid #dedddd;
                    z-index:100000000;
                    
                }
                wplrpprompticon{
                    width: 20%;
                    float: left;
                    display: block;
                    margin: 0 auto;
                    text-align: left;
                }
                wplrpPromptIcon img{width:70px;}
                wplrpPromptText{
                    width: 75%;
                    float: left;
                    text-align: left;
                    font-size: 16px;
                    padding-left: 10px;
                    color:<?php echo esc_html($this->wplrp_web_push['prompt']['message_color']);?>
                }
                wplrpPromptText{
                }
                wplrpPromptHeading{
                    display: block;
                    position: relative;
                    text-align: initial;
                    font-weight: 600;
                    font-size: 16px;
                    
                    padding-bottom: 7px;
                    font-family: sans-serif !important;
                }
                wplrpPromptMessage{
                    font-family: sans-serif !important;
                    display: block;
                    font-size: 16px;
                    text-align: initial;
                    font-weight: 100;
                    line-height: 20px;
                }
                wplrpPromptButtons{
                    position: relative;
                    display: inline-block;
                    clear: both;
                    width: 100%;
                    font-size: 10px;
                    padding-top: 20px;
                    padding-bottom: 0px;
                    direction: ltr;  
                    text-align:right;              
                }
                wplrpPromptButton{
                  cursor: pointer;
                  transition: all 60ms ease-in-out;
                  text-align: center;
                  white-space: nowrap;
                  color: #333;
                  border: 0 none;
                  border-radius: 2px;
                  padding: 7px 20px;
                  font-size: 16px;
                  font-family: sans-serif !important;
                  display: inline-block;
                  background: blue;
                  min-width: 105px;
                  margin: 0 5px;
                  color: #fff;
                  box-sizing: border-box;
                }
                wplrppromptdismissbtn wplrppromptbutton{
                    background:<?php echo esc_html($this->wplrp_web_push['prompt']['dismiss_button_background_color']);?>;
                    color:<?php echo esc_html($this->wplrp_web_push['prompt']['dismiss_button_text_color']);?>;
                }
                wplrppromptapprovebtn wplrppromptbutton{
                    background:<?php echo esc_html($this->wplrp_web_push['prompt']['allow_button_background_color']);?>;
                    color:<?php echo esc_html($this->wplrp_web_push['prompt']['allow_button_text_color']);?>;

                }
                @media(max-width:550px){
                  wplrppromptcontainer{
                     width: 100%;
                     left: 0;
                     bottom: 0;
                     top: unset;
                     position: fixed;
                     box-shadow: 1px 1px 10px #adadad;
                  }
                }
            </style>
<wplrpPromptWrapper>            
<wplrpPromptContainer>
   <wplrpPrompt>
      <wplrpPromptIcon>
         <img src="<?php echo esc_url($this->wplrp_web_push['prompt']['logo']);?>" alt=''>
      </wplrpPromptIcon>
      <wplrpPromptText>
         <wplrpPromptHeading>
         </wplrpPromptHeading>
         <wplrpPromptMessage>
            <?php echo esc_html($this->wplrp_web_push['prompt']['message']);?>
         </wplrpPromptMessage>
      </wplrpPromptText>
      <wplrpPromptButtons>
         <wplrpPromptDismissBtn>
            <wplrpPromptButton onclick="letsrecover('prompt_action','Dismiss')"><?php echo esc_html($this->wplrp_web_push['prompt']['dismiss_button_text']);?></wplrpPromptButton>
         </wplrpPromptDismissBtn>
         <wplrpPromptApproveBtn>
            <wplrpPromptButton onclick="letsrecover('prompt_action','Approve')"><?php echo esc_html($this->wplrp_web_push['prompt']['allow_button_text']);?></wplrpPromptButton>
         </wplrpPromptApproveBtn>
      </wplrpPromptButtons>
   </wplrpPrompt>
</wplrpPromptContainer>
</wplrpPromptWrapper>