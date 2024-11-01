jQuery(document).ready(function($) {

	'use strict';

	window.DAEXTSOLISC = {};
	window.DAEXTSOLISC.liveMatchesInDom = [];

	//Get the IDs of all the live matches and store them in a global variable
	$('.daextsolisc-container[data-live="1"]').each(function() {

		'use strict';

		let matchId = parseInt($(this).data('match-id'), 10);
		window.DAEXTSOLISC.liveMatchesInDom.push(matchId);

	});

	/*
	 * If there is at least one live match set the interval used to update the
	 * matches.
	 */
	if(window.DAEXTSOLISC.liveMatchesInDom.length > 0){
		setInterval(update_match, window.DAEXTSOLISC_PARAMETERS.updateTime * 1000 );
	}

	/**
	 * This function does what follows:
	 *
	 * - Sends an AJAX request to retrieve the data of all the live matches.
	 * - Updates the score and the events of all the live matches.
	 *
	 * Live matches are matches with the "Live" field set to "Yes". Matches that
	 * are not live are not considered by this function.
	 */
	function update_match(){

		'use strict';

		let data = {
			'action': 'daextsolisc_get_match_data',
			'security': window.DAEXTSOLISC_PARAMETERS.nonce,
			data: window.DAEXTSOLISC.liveMatchesInDom,
		};

		//perform the ajax request
		$.post( window.DAEXTSOLISC_PARAMETERS.ajaxUrl, data, function( jsonData ) {

			'use strict';

			//Convert the retrieved JSON data into an array
			let matchesData = JSON.parse(jsonData);

			//Update the score and events of all the live matches
			$.each(matchesData, function(index, match) {

				//Update the score only if the score hash has changed
				let score_hash = $('.daextsolisc-container[data-match-id="' + match['match_id'] + '"] .daextsolisc-head-center').data('hash');
				if(score_hash !== match['score_hash']){

					//Set the new score hash in the DOM
					$('.daextsolisc-container[data-match-id="' + match['match_id'] + '"] .daextsolisc-head-center').data('hash', match.score_hash);

					//update the score
					$('.daextsolisc-container[data-match-id="' + match['match_id'] + '"] .daextsolisc-head-center').fadeOut(400, function(){
						$('.daextsolisc-container[data-match-id="' + match['match_id'] + '"] .daextsolisc-team-1-score').text(match.team_1_score);
						$('.daextsolisc-container[data-match-id="' + match['match_id'] + '"] .daextsolisc-team-2-score').text(match.team_2_score);
						$('.daextsolisc-container[data-match-id="' + match['match_id'] + '"] .daextsolisc-team-1-first-leg-score').text('(' + match.team_1_first_leg_score);
						$('.daextsolisc-container[data-match-id="' + match['match_id'] + '"] .daextsolisc-team-2-first-leg-score').text(match.team_2_first_leg_score + ')');
						$('.daextsolisc-container[data-match-id="' + match['match_id'] + '"] .daextsolisc-head-center').fadeIn(400);
					});

				}

				//Update the events only if the event hash has changed
				let events_hash = $('.daextsolisc-container[data-match-id="' + match['match_id'] + '"] .daextsolisc-body').data('hash');
				if(events_hash !== match.events_hash){

					//Set the new event hash in the DOM
					$('.daextsolisc-container[data-match-id="' + match['match_id'] + '"] .daextsolisc-body').data('hash', match.events_hash);

					//Generate the HTML code of the events
					let html = '';
					$.each(match['events'], function(index, event) {

						let row_side_class = null;
						let additional_information = null;
						let even_or_odd = (index % 2 == 0) ? "odd" : "even";

						if( parseInt(event.team, 10) === 0 ){
							row_side_class = 'daextsolisc-row-left';
						}else{
							row_side_class = 'daextsolisc-row-right';
						}

						html += '<div class="daextsolisc-row daextsolisc-row-' + even_or_odd + ' ' + row_side_class + '">';
						html += '<div class="daextsolisc-event-type daextsolisc-event-icon"><img src="' + event.event_icon + '"></div>';
						html += '<div class="daextsolisc-minute">' + event.minute + '\'</div>';

						if(event.additional_information.length > 0){
							additional_information = ' <span class="daextsolisc-additional-information">' + event.additional_information + '</span>';
						}else{
							additional_information = '';
						}

						html += '<div class="daextsolisc-event-description">' + event.description + additional_information + '</div>';
						html += '</div>';//.daextsolisc-row

					});

					//Update the DOM with the new match events
					$('.daextsolisc-container[data-match-id="' + match['match_id'] + '"] .daextsolisc-body').fadeOut(400, function(){
						$('.daextsolisc-container[data-match-id="' + match['match_id'] + '"] .daextsolisc-body').html(html);
						$('.daextsolisc-container[data-match-id="' + match['match_id'] + '"] .daextsolisc-body').fadeIn(400);
					});

				}
				
			});

		});

	}

});