<?php
/**
 *
 * @author Ananaskelly
 */
namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Auth;
use Carbon\Carbon;

class Game extends Model
{
    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'games';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = array(
        'id',
        'game_type_id',
        'private',
        'time_started',
        'time_finished',
        'winner',
        'bonus'
    );
    /**
     * Constant for user color
     *
     * @var const int
     */
    const WHITE = 0;
    const BLACK = 1;
    /**
     * Disable Timestamps fields
     *
     * @var boolean
     */
    public $timestamps = false;
    
    /**
     *
     * @return void
     */
    public static function init(Game $game)
    {
        
    }
    /**
     * Create or modified user game
     *
     * @return Game
     */
    public static function createGame(GameType $gameType, $private, User $user)
    {
        $gameStatus = $user->getCurrentGameStatus();
        switch ($gameStatus) {
            case User::NO_GAME:
                $game = Game::where(['private' => $private, 'game_type_id' => $gameType->id, 'time_started' => null])
                    ->orderBy('id', 'ask')
                    ->first();
                $userGame = new UserGame();
                if ($game === null) {
                    $game = new Game();
                    $game->private = $private;
                    $game->gameType()->associate($gameType);
                    $game->save();
                    $userGame->user()->associate($user);
                    $userGame->game()->associate($game);
                    $userGame->color = (bool)rand(0, 1);
                    $userGame->save();
                } else {
                    $opposite = UserGame::where('game_id', $game->id)->first();
                    $userGame->user()->associate($user);
                    $userGame->game()->associate($game);
                    $userGame->color = !$opposite->color;
                    $userGame->save();
                    $game->update(['time_started' => Carbon::now()]);
                    Game::init($game);
                }
                return $game;
            case User::SEARCH_GAME:
                $lastUserGame = $user->userGames->sortBy('id')->last();
                $game = Game::find($lastUserGame->game_id);
                $game->private = $private;
                $game->gameType()->associate($gameType);
                $game->save();
                return $game;
            default:
                return null;
        }
                        
    }
    
    /**
     *
     * @return void
     */
    public function cancelGame()
    {
        if (is_null($this->time_started)) {
            $this->delete();
        }
    }
    /**
     *
     * @return BoardInfo[]
     */
    public function boardInfos()
    {
        return $this->hasMany('App\BoardInfo');
    }
    
    /**
     *
     * @return TurnInfo[]
     */
    public function turnInfos()
    {
        return $this->hasMany('App\TurnInfo');
    }
    
    /**
     *
     * @return UserGame[]
     */
    public function userGames()
    {
        return $this->hasMany('App\UserGame');
    }
    
    /**
     *
     * @return GameType
     */
    public function gameType()
    {
        return $this->belongsTo('App\GameType');
    }
    /**
     * Get last user turn (color)
     *
     * @return int
     */
    public function getLastUserTurn()
    {
        if ($this->turnInfos->orderBy('id', 'desc')->last()->user_turn == '0') {
            return Game::WHITE;
        } else {
            return Game::BLACK;
        }
    }
}
