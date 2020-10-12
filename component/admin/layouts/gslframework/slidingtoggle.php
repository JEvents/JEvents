<style>
		    .sliding-toggle {
	height:30px;
		    }
		    .sliding-toggle input.gsl-checkbox {
	font-size: .6rem;
			    margin-top: 0;
			    position: relative;
			    -webkit-appearance: none;
			    outline: none;
			    width: 40px;
			    height: 24px;
			    border: 2px solid #D6D6D6;
			    border-radius: 12px;
			    flex-shrink: 0;
		    }
		    .sliding-toggle input.gsl-checkbox:after {
	content: "";
	position: absolute;
	top: 2px;
			     left: 2px;
			     background: #FFF;
			     width: 15px;
			     height: 15px;
			     border-radius: 50%;
			     transition: all 250ms ease 20ms;
			     box-shadow: .05em .25em .5em rgba(0, 0, 0, 0.2);
		    }
		    .sliding-toggle input.gsl-checkbox:checked {
	background-color: transparent;
			    box-shadow:inset 30px 0 0 0 #4ed164;
			    border-color:#D6D6D6;
			    background-image:none;
		    }
		    .sliding-toggle input.gsl-checkbox:checked:after {
	left:20px;
			    box-shadow:0 0 6px rgba(0,0,0,0.2);
		    }
   </style>

<li class="sliding-toggle">
	<input type="checkbox" class="gsl-checkbox" value="1" />
</li>

<?php
