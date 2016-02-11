<!DOCTYPE html>
<html>
    <head>
        <title>Yapper! Instant messaging for teams</title>
        
        <link rel="stylesheet" href="style.css" />
        
        <script src="lib/ajax.js"></script>
        
    </head>
    <body>
        <header>
            <div class="logo">
                <h1>
                    Yapper!
                    <span class="subtitle">Instant messaging for teams</span>
                </h1>
            </div>
            <div class="profile">
                <img src="img/profile_pic.png">
                <span class="name">Oliver Pennington</span>
                <span class="description">Design Team</span>
            </div>
        </header>
        
        <section class="channel-container">
            <div class="channel">
                <span class="channel-title">#general</span>
                <div class="channel-chat">
                    <span class="message">
                        Hey, how was your weekend?
                        <span class="sender">
                            10:50am | Joe Bloggs
                        </span>
                    </span>
                    <span class="message">
                        It was great, thanks! You?
                        <span class="sender">
                            10:51am | Oliver Pennington
                        </span>
                    </span>
                    <span class="message">
                        Yeah fine, thanks. Have you seen the new coffee machine the office has?
                        <span class="sender">
                            10:52am | Joe Bloggs
                        </span>
                    </span>
                    <span class="message">
                        Sure have! That thing makes a fine latte &#59;&#41;
                        <span class="sender">
                            10:53am | Oliver Pennington
                        </span>
                    </span>
                </div>
                <div class="channel-input">
                    <input type="text" placeholder="Send a message to #general">
                </div>
            </div>
            <div class="channel">
                <span class="channel-title">#microsoft-project <span class="num-new-messages">2</span></span>
                <div class="channel-chat">
                    <span class="message">
                        <span class="mention">@ollie</span> What's the ETA on the initial draft push?
                        <span class="sender">
                            12:10pm | Eric Wright, Marketing
                        </span>
                    </span>
                    <span class="message">
                        It's uploading as we speak! <span class="mention">@joeb</span> The initial draft should be sufficient to show as the prototype.
                        <span class="sender">
                            12:10pm | Oliver Pennington, Design
                        </span>
                    </span>
                    <span class="message new">
                        <span class="mention">@joeb</span> Remember to specifically tell them it's only a draft.
                        <span class="sender">
                            12:11pm | Eric Wright, Marketing
                        </span>
                    </span>
                    <span class="message new">
                        No worries guys, see you at lunch &#59;&#41;
                        <span class="sender">
                            12:11pm | Joe Bloggs, Sales
                        </span>
                    </span>
                </div>
                <div class="channel-input">
                    <input type="text" placeholder="Send a message to #microsoft-project">
                </div>
            </div>
            <div class="channel" style="margin-right:0!important;">
                <span class="channel-title">#design-team-general</span>
                <div class="channel-chat">
                    <span class="message">
                        Does anyone know how you prevent chrome from using 8GB+ of ram??
                        <span class="sender">
                            11:00am | Andre Young, Design
                        </span>
                    </span>
                    <span class="message">
                        It's a know issue. We'll have to wait for our Google overlords to fix it, unfortunately.
                        <span class="sender">
                            11:02am | Oliver Pennington, Design
                        </span>
                    </span>
                    <span class="message">
                        You could always just use Firefox?
                        <span class="sender">
                            11:11am | O'Shea Jackson, Design
                        </span>
                    </span>
                    <span class="message">
                        Yeah I might have to... Shame, I much prefer Chrome.
                        <span class="sender">
                            11:12am | Andre Young, Design
                        </span>
                    </span>
                </div>
                <div class="channel-input">
                    <input type="text" placeholder="Send a message to #microsoft-project">
                </div>
            </div>
        </section>
        
    </body>
</html>