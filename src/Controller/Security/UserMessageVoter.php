<?php
/**
 * Created by PhpStorm.
 * User: virga
 * Date: 2018-05-25
 * Time: 15:03
 */

namespace App\Controller\Security;


    use App\Entity\User;
    use App\Entity\UserMessage;
    use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
    use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
    use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserMessageVoter extends Voter
{
    const SEE = 'see';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::SEE))) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // ROLE_ADMIN can do anything!
        if ($this->decisionManager->decide($token, array('ROLE_SUPER_ADMIN'))) {
            return true;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        /** @var UserMessage */
        $userMessageEntity = $subject;

        switch ($attribute) {
            case self::SEE:
                return $this->canSee($userMessageEntity, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    // User can only see his messages
    private function canSee(User $userMessage, User $user)
    {
        return $user === $userMessage;
    }

}