<?php
require_once __DIR__ . '/BaseModel.php';

class ReviewVoteModel extends BaseModel {
    protected $table = 'review_votes';

    public function vote(int $reviewId, int $userId, string $voteType): void
    {
        if (!in_array($voteType, ['up', 'down'], true)) {
            throw new InvalidArgumentException("Invalid vote type");
        }

        $stmt = $this->db->prepare("SELECT user_id FROM reviews WHERE id = ?");
        if (!$stmt) { throw new Exception($this->db->error); }
        $stmt->bind_param("i", $reviewId);
        $stmt->execute();
        $reviewOwner = $stmt->get_result()->fetch_assoc()['user_id'] ?? null;
        $stmt->close();

        if ($reviewOwner == $userId) {
            throw new Exception("You cannot vote on your own review.");
        }

        $this->db->begin_transaction();

        try {
            $stmt = $this->db->prepare(
                "SELECT vote_type FROM review_votes WHERE review_id = ? AND user_id = ?"
            );
            if (!$stmt) {
                throw new Exception($this->db->error);
            }

            $stmt->bind_param("ii", $reviewId, $userId);
            $stmt->execute();

            $result = $stmt->get_result();
            $existingVote = $result->fetch_assoc()['vote_type'] ?? null;
            $stmt->close();

            if ($existingVote === $voteType) {

                $stmt = $this->db->prepare(
                    "DELETE FROM review_votes WHERE review_id = ? AND user_id = ?"
                );
                if (!$stmt) {
                    throw new Exception($this->db->error);
                }

                $stmt->bind_param("ii", $reviewId, $userId);
                $stmt->execute();
                $stmt->close();

                $column = $voteType === 'up' ? 'upvotes' : 'downvotes';

                $stmt = $this->db->prepare(
                    "UPDATE reviews SET $column = GREATEST($column - 1, 0) WHERE id = ?"
                );
                $stmt->bind_param("i", $reviewId);
                $stmt->execute();
                $stmt->close();
            }

            elseif ($existingVote !== null) {

                $stmt = $this->db->prepare(
                    "UPDATE review_votes SET vote_type = ? WHERE review_id = ? AND user_id = ?"
                );
                if (!$stmt) {
                    throw new Exception($this->db->error);
                }

                $stmt->bind_param("sii", $voteType, $reviewId, $userId);
                $stmt->execute();
                $stmt->close();

                if ($voteType === 'up') {
                    $stmt = $this->db->prepare(
                        "UPDATE reviews
                        SET upvotes = upvotes + 1,
                            downvotes = GREATEST(downvotes - 1, 0)
                        WHERE id = ?"
                    );
                } else {
                    $stmt = $this->db->prepare(
                        "UPDATE reviews
                        SET downvotes = downvotes + 1,
                            upvotes = GREATEST(upvotes - 1, 0)
                        WHERE id = ?"
                    );
                }

                $stmt->bind_param("i", $reviewId);
                $stmt->execute();
                $stmt->close();
            }

            else {
                $stmt = $this->db->prepare(
                    "INSERT INTO review_votes (review_id, user_id, vote_type)
                    VALUES (?, ?, ?)"
                );
                if (!$stmt) {
                    throw new Exception($this->db->error);
                }

                $stmt->bind_param("iis", $reviewId, $userId, $voteType);
                $stmt->execute();
                $stmt->close();

                $column = $voteType === 'up' ? 'upvotes' : 'downvotes';

                $stmt = $this->db->prepare(
                    "UPDATE reviews SET $column = $column + 1 WHERE id = ?"
                );
                $stmt->bind_param("i", $reviewId);
                $stmt->execute();
                $stmt->close();
            }

            $this->db->commit();

        } catch (Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    private function addVote(int $reviewId, int $userId, string $voteType): void {
        $this->db->prepare(
            "INSERT INTO review_votes (review_id, user_id, vote_type)
            VALUES (?, ?, ?)"
        )->bind_param("iis", $reviewId, $userId, $voteType)->execute();

        if ($voteType === 'up') {
            $this->db->query("UPDATE reviews SET upvotes = upvotes + 1 WHERE id = $reviewId");
        } else {
            $this->db->query("UPDATE reviews SET downvotes = downvotes + 1 WHERE id = $reviewId");
        }
    }

    private function removeVote(int $reviewId, int $userId, string $voteType): void {
        $this->db->prepare(
            "DELETE FROM review_votes WHERE review_id = ? AND user_id = ?"
        )->bind_param("ii", $reviewId, $userId)->execute();

        if ($voteType === 'up') {
            $this->db->query(
                "UPDATE reviews SET upvotes = GREATEST(upvotes - 1, 0) WHERE id = $reviewId"
            );
        } else {
            $this->db->query(
                "UPDATE reviews SET downvotes = GREATEST(downvotes - 1, 0) WHERE id = $reviewId"
            );
        }
    }

    private function switchVote(
        int $reviewId,
        int $userId,
        string $newVote,
        string $oldVote
    ): void {
        $this->db->prepare(
            "UPDATE review_votes SET vote_type = ? WHERE review_id = ? AND user_id = ?"
        )->bind_param("sii", $newVote, $reviewId, $userId)->execute();

        if ($oldVote === 'up') {
            $this->db->query(
                "UPDATE reviews
                SET upvotes = GREATEST(upvotes - 1, 0),
                    downvotes = downvotes + 1
                WHERE id = $reviewId"
            );
        } else {
            $this->db->query(
                "UPDATE reviews
                SET downvotes = GREATEST(downvotes - 1, 0),
                    upvotes = upvotes + 1
                WHERE id = $reviewId"
            );
        }
    }

    public function getUserVote(int $reviewId, int $userId): ?string {
        $stmt = $this->db->prepare(
            "SELECT vote_type FROM review_votes WHERE review_id = ? AND user_id = ?"
        );
        $stmt->bind_param("ii", $reviewId, $userId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        return $res['vote_type'] ?? null;
    }
}
