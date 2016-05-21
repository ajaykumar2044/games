/**
 *
 * @author Ananaskelly
 */
define(['./gameConfig'], function (gameConfig) {
    return {
        compile: function () {
            $('.cell').has('div.figure').addClass('busy');
            var userId = $('.board').attr('data-user');
            var white = $('.board').attr('data-player-white');
            var black = $('.board').attr('data-player-black');
            var userBlack = $('.board').attr('data-player-login-black');
            var userWhite = $('.board').attr('data-player-login-white');
            if (userId === white || userId === black) {
                if (userId === white) {
                    gameConfig.setConfig('userState', 'white');
                    gameConfig.setConfig('revert', true);
                    $('#user1').attr('data-color', 'black');
                    $('#user2').attr('data-color', 'white');
                    $('#user1-img').attr('src', '/img/black.png');
                    $('#user2-img').attr('src', '/img/white.png');
                } else {
                    gameConfig.setConfig('userState', 'black');
                    $('#user1').attr('data-color', 'white');
                    $('#user2').attr('data-color', 'black');
                    $('#user2-img').attr('src', '/img/black.png');
                    $('#user1-img').attr('src', '/img/white.png');
                }
                $('[data-color=black]').text(userBlack);
                $('[data-color=white]').text(userWhite);
            }
        }
    }
})

