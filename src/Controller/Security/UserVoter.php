<?php
/**
 * Created by PhpStorm.
 * User: virga
 * Date: 2018-05-25
 * Time: 15:03
 */

namespace App\Controller\Security;


    use App\Entity\User;
    use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
    use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
    use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    const EDIT = 'edit';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::EDIT))) {
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

        /** @var User */
        $userEntity = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($userEntity, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    // User can only edit his profile
    private function canEdit(User $userEntity, User $user)
    {
        return $user->getId() === $userEntity->getId();
    }

}