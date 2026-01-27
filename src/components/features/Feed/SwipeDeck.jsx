import React, { useState } from 'react';
import styled from 'styled-components';
import { motion, useMotionValue, useTransform } from 'framer-motion';
import JobCard from './JobCard';

const DeckContainer = styled.div`
  position: relative;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  perspective: 1000px;
`;

const CardBehind = styled.div`
  position: absolute;
  width: 100%;
  height: 100%;
  z-index: 5;
  filter: brightness(0.95);
  transform-origin: 50% 100%;
  pointer-events: none;
`;

const EmptyFeed = styled.div`
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  padding: ${({ theme }) => theme.spacing.xl};
  color: ${({ theme }) => theme.colors.textMuted};

  h2 {
    font-size: ${({ theme }) => theme.typography.fontSizes.xl};
    color: ${({ theme }) => theme.colors.secondary};
    margin-bottom: ${({ theme }) => theme.spacing.sm};
  }

  p {
    font-size: ${({ theme }) => theme.typography.fontSizes.md};
  }
`;

const SwipeIndicator = styled(motion.div)`
  position: absolute;
  top: 40px;
  z-index: 20;
  border: 4px solid ${({ $type }) => ($type === 'like' ? '#2ECC71' : '#E74C3C')};
  border-radius: ${({ theme }) => theme.borderRadius.md};
  padding: 5px 10px;
  color: ${({ $type }) => ($type === 'like' ? '#2ECC71' : '#E74C3C')};
  font-size: 32px;
  font-weight: bold;
  transform: ${({ $type }) => ($type === 'like' ? 'rotate(-20deg)' : 'rotate(20deg)')};
  left: ${({ $type }) => ($type === 'like' ? '40px' : 'auto')};
  right: ${({ $type }) => ($type === 'nope' ? '40px' : 'auto')};
`;

const MotionCard = styled(motion.div)`
  width: 100%;
  height: 100%;
  position: absolute;
  z-index: 10;
  cursor: grab;

  &:active {
    cursor: grabbing;
  }
`;

const SwipeDeck = ({ jobs, onSwipe }) => {
  const [index, setIndex] = useState(0);
  const [exitX, setExitX] = useState(0);

  const x = useMotionValue(0);
  const rotate = useTransform(x, [-200, 200], [-15, 15]);

  // Color overlays logic
  const likeOpacity = useTransform(x, [20, 150], [0, 1]);
  const nopeOpacity = useTransform(x, [-150, -20], [1, 0]);

  const handleDragEnd = (e, info) => {
    if (info.offset.x > 100) {
      setExitX(200);
      onSwipe('right', jobs[index]);
      setIndex(index + 1);
    } else if (info.offset.x < -100) {
      setExitX(-200);
      onSwipe('left', jobs[index]);
      setIndex(index + 1);
    }
  };

  const currentJob = jobs[index];

  if (index >= jobs.length) {
    return (
      <EmptyFeed>
        <h2>אין עוד משרות כרגע :(</h2>
        <p>חזור מאוחר יותר או הרחב את הסינון שלך.</p>
      </EmptyFeed>
    );
  }

  return (
    <DeckContainer>
      {/* Render next card below for depth effect */}
      {index + 1 < jobs.length && (
        <CardBehind>
          <JobCard job={jobs[index + 1]} style={{ transform: 'scale(0.9)', top: 20 }} />
        </CardBehind>
      )}

      <MotionCard
        style={{ x, rotate }}
        drag="x"
        dragConstraints={{ left: 0, right: 0 }}
        dragElastic={0.7}
        onDragEnd={handleDragEnd}
        initial={{ scale: 1, x: 0 }}
        animate={{ scale: 1, x: 0, opacity: 1 }}
        exit={{ x: exitX, opacity: 0, transition: { duration: 0.2 } }}
        key={currentJob.id}
      >
        {/* Swipe Indicators */}
        <SwipeIndicator $type="like" style={{ opacity: likeOpacity }}>
          LIKE
        </SwipeIndicator>
        <SwipeIndicator $type="nope" style={{ opacity: nopeOpacity }}>
          NOPE
        </SwipeIndicator>

        <JobCard job={currentJob} />
      </MotionCard>
    </DeckContainer>
  );
};

export default SwipeDeck;
