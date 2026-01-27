import React from 'react';
import { MessageCircle, ChevronRight } from 'lucide-react';

const MatchesList = ({ matches, onSelectMatch }) => {
    return (
        <div className="matches-list-container">
            <h2 className="section-title">התאמות חדשות</h2>
            <div className="new-matches-scroll">
                {matches.map((match) => (
                    <div key={match.id} className="new-match-avatar" onClick={() => onSelectMatch(match)}>
                        <img src={match.image} alt={match.company} />
                        {match.hasUnread && <div className="indicator"></div>}
                    </div>
                ))}
            </div>

            <h2 className="section-title">הודעות</h2>
            <div className="messages-list">
                {matches.map((match) => (
                    <div key={match.id} className="message-item" onClick={() => onSelectMatch(match)}>
                        <div className="avatar-container">
                            <img src={match.image} alt={match.company} className="avatar" />
                            {match.isOnline && <div className="online-badge"></div>}
                        </div>
                        <div className="message-content">
                            <div className="message-header">
                                <span className="company-name">{match.company}</span>
                                <span className="message-time">{match.lastActive}</span>
                            </div>
                            <p className="last-message">{match.lastMessage}</p>
                        </div>
                    </div>
                ))}
            </div>

            <style>{`
        .matches-list-container {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
        }

        .section-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--color-secondary);
            margin-bottom: 1rem;
            margin-top: 0.5rem;
        }

        .new-matches-scroll {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
            scrollbar-width: none; /* Firefox */
        }
        .new-matches-scroll::-webkit-scrollbar {
            display: none; /* Chrome/Safari */
        }

        .new-match-avatar {
            position: relative;
            flex-shrink: 0;
            cursor: pointer;
        }

        .new-match-avatar img {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--color-primary);
            padding: 2px;
        }

        .indicator {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 14px;
            height: 14px;
            background-color: #E74C3C;
            border: 2px solid white;
            border-radius: 50%;
        }

        .messages-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .message-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: white;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .message-item:active {
            background-color: #f9fafb;
        }

        .avatar-container {
            position: relative;
        }

        .avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;
        }

        .online-badge {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 12px;
            height: 12px;
            background-color: #2ECC71;
            border: 2px solid white;
            border-radius: 50%;
        }

        .message-content {
            flex: 1;
            min-width: 0; /* Text truncation fix */
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.25rem;
        }

        .company-name {
            font-weight: 600;
            font-size: 1rem;
        }

        .message-time {
            font-size: 0.75rem;
            color: var(--color-text-muted);
        }

        .last-message {
            font-size: 0.875rem;
            color: var(--color-text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
      `}</style>
        </div>
    );
};

export default MatchesList;
