import React, { useState, useEffect, useRef } from 'react';
import styled from 'styled-components';
import { ArrowRight, Send, Paperclip, Loader } from 'lucide-react';
import { useAuth } from '../../../contexts/AuthContext';
import { messagesAPI } from '../../../services/api';
import { Avatar } from '../../ui';

const ChatContainer = styled.div`
  flex: 1;
  display: flex;
  flex-direction: column;
  background-color: ${({ theme }) => theme.colors.background};
  height: 100vh;
`;

const ChatHeader = styled.header`
  display: flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.md};
  padding: ${({ theme }) => theme.spacing.md};
  background: white;
  box-shadow: ${({ theme }) => theme.shadows.sm};
  z-index: 10;
`;

const BackButton = styled.button`
  background: none;
  border: none;
  cursor: pointer;
  padding: ${({ theme }) => theme.spacing.xs};
  display: flex;
  align-items: center;
  justify-content: center;
  color: ${({ theme }) => theme.colors.text};
  border-radius: ${({ theme }) => theme.borderRadius.full};
  transition: background-color ${({ theme }) => theme.transitions.fast};

  &:hover {
    background-color: ${({ theme }) => theme.colors.surfaceHover};
  }
`;

const HeaderInfo = styled.div`
  display: flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.sm};
`;

const HeaderText = styled.div`
  h3 {
    font-size: ${({ theme }) => theme.typography.fontSizes.md};
    font-weight: ${({ theme }) => theme.typography.fontWeights.semibold};
    color: ${({ theme }) => theme.colors.text};
  }

  span {
    font-size: ${({ theme }) => theme.typography.fontSizes.xs};
    color: ${({ theme }) => theme.colors.primary};
  }
`;

const MessagesContainer = styled.div`
  flex: 1;
  overflow-y: auto;
  padding: ${({ theme }) => theme.spacing.md};
  display: flex;
  flex-direction: column;
`;

const MessagesWrapper = styled.div`
  display: flex;
  flex-direction: column;
  gap: ${({ theme }) => theme.spacing.sm};
  justify-content: flex-end;
  min-height: 100%;
`;

const MessageBubble = styled.div`
  max-width: 75%;
  padding: ${({ theme }) => theme.spacing.sm} ${({ theme }) => theme.spacing.md};
  border-radius: ${({ theme }) => theme.borderRadius.lg};
  position: relative;
  align-self: ${({ $isMe }) => ($isMe ? 'flex-end' : 'flex-start')};
  background-color: ${({ $isMe, theme }) =>
    $isMe ? theme.colors.primary : 'white'};
  color: ${({ $isMe }) => ($isMe ? 'white' : 'inherit')};
  border-bottom-left-radius: ${({ $isMe, theme }) =>
    $isMe ? theme.borderRadius.lg : theme.borderRadius.xs};
  border-bottom-right-radius: ${({ $isMe, theme }) =>
    $isMe ? theme.borderRadius.xs : theme.borderRadius.lg};
  box-shadow: ${({ $isMe, theme }) => ($isMe ? 'none' : theme.shadows.sm)};
`;

const BubbleContent = styled.div`
  font-size: ${({ theme }) => theme.typography.fontSizes.sm};
  line-height: ${({ theme }) => theme.typography.lineHeights.relaxed};
`;

const BubbleTime = styled.div`
  font-size: ${({ theme }) => theme.typography.fontSizes.xs};
  opacity: 0.7;
  margin-top: ${({ theme }) => theme.spacing.xs};
  text-align: left;
`;

const InputArea = styled.div`
  padding: ${({ theme }) => theme.spacing.sm} ${({ theme }) => theme.spacing.md};
  background: white;
  display: flex;
  align-items: center;
  gap: ${({ theme }) => theme.spacing.sm};
  border-top: 1px solid ${({ theme }) => theme.colors.borderLight};
`;

const InputWrapper = styled.div`
  flex: 1;
  background: ${({ theme }) => theme.colors.background};
  border-radius: ${({ theme }) => theme.borderRadius.full};
  padding: 0 ${({ theme }) => theme.spacing.md};

  input {
    width: 100%;
    height: 44px;
    border: none;
    background: transparent;
    outline: none;
    font-family: inherit;
    font-size: ${({ theme }) => theme.typography.fontSizes.md};
    color: ${({ theme }) => theme.colors.text};

    &::placeholder {
      color: ${({ theme }) => theme.colors.textMuted};
    }
  }
`;

const IconButton = styled.button`
  width: 44px;
  height: 44px;
  border-radius: ${({ theme }) => theme.borderRadius.full};
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color ${({ theme }) => theme.transitions.fast};
  background: ${({ $primary, theme }) =>
    $primary ? theme.colors.primary : 'transparent'};
  color: ${({ $primary, theme }) =>
    $primary ? 'white' : theme.colors.textMuted};

  &:hover {
    background: ${({ $primary, theme }) =>
      $primary ? theme.colors.primaryDark : theme.colors.surfaceHover};
  }

  &:disabled {
    background-color: ${({ theme }) => theme.colors.borderLight};
    cursor: default;
  }
`;

const LoadingContainer = styled.div`
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  color: ${({ theme }) => theme.colors.textMuted};

  svg {
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    from {
      transform: rotate(0deg);
    }
    to {
      transform: rotate(360deg);
    }
  }
`;

const ChatInterface = ({ match, onBack }) => {
  const [messages, setMessages] = useState([]);
  const [inputText, setInputText] = useState('');
  const [loading, setLoading] = useState(true);
  const [sending, setSending] = useState(false);
  const messagesEndRef = useRef(null);
  const { user, isAuthenticated } = useAuth();

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  };

  useEffect(() => {
    scrollToBottom();
  }, [messages]);

  // Fetch messages on mount
  useEffect(() => {
    const fetchMessages = async () => {
      if (!isAuthenticated || !match.id) {
        // Use mock data if not authenticated
        setMessages([
          {
            id: 1,
            text: match.lastMessage,
            sender: 'them',
            time: match.lastActive,
          },
        ]);
        setLoading(false);
        return;
      }

      try {
        const data = await messagesAPI.getMessages(match.id);
        if (data && data.length > 0) {
          const transformedMessages = data.map((msg) => ({
            id: msg.id,
            text: msg.content,
            sender: msg.senderId === user.id ? 'me' : 'them',
            time: new Date(msg.createdAt).toLocaleTimeString([], {
              hour: '2-digit',
              minute: '2-digit',
            }),
          }));
          setMessages(transformedMessages);
        } else {
          // No messages yet, start with empty or system message
          setMessages([
            {
              id: 'system-1',
              text: 'התחילו שיחה חדשה!',
              sender: 'system',
              time: '',
            },
          ]);
        }
      } catch (error) {
        console.error('Error fetching messages:', error);
        // Fallback to mock
        setMessages([
          {
            id: 1,
            text: match.lastMessage || 'שלום! נעים להכיר',
            sender: 'them',
            time: match.lastActive || 'היום',
          },
        ]);
      } finally {
        setLoading(false);
      }
    };

    fetchMessages();

    // Poll for new messages every 5 seconds
    const interval = setInterval(fetchMessages, 5000);
    return () => clearInterval(interval);
  }, [isAuthenticated, match.id, user?.id]);

  const handleSend = async () => {
    if (!inputText.trim()) return;

    const messageText = inputText.trim();
    setInputText('');

    // Optimistic update
    const tempMessage = {
      id: `temp-${Date.now()}`,
      text: messageText,
      sender: 'me',
      time: new Date().toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit',
      }),
    };
    setMessages((prev) => [...prev, tempMessage]);

    if (!isAuthenticated) {
      // Mock reply for demo
      setTimeout(() => {
        setMessages((prev) => [
          ...prev,
          {
            id: Date.now() + 1,
            text: 'תודה, נשמח לקבוע ראיון!',
            sender: 'them',
            time: new Date().toLocaleTimeString([], {
              hour: '2-digit',
              minute: '2-digit',
            }),
          },
        ]);
      }, 2000);
      return;
    }

    try {
      setSending(true);
      const result = await messagesAPI.sendMessage(match.id, messageText);
      // Update temp message with real data
      setMessages((prev) =>
        prev.map((msg) =>
          msg.id === tempMessage.id
            ? {
                id: result.id,
                text: result.content,
                sender: 'me',
                time: new Date(result.createdAt).toLocaleTimeString([], {
                  hour: '2-digit',
                  minute: '2-digit',
                }),
              }
            : msg
        )
      );
    } catch (error) {
      console.error('Error sending message:', error);
      // Remove temp message on error
      setMessages((prev) => prev.filter((msg) => msg.id !== tempMessage.id));
    } finally {
      setSending(false);
    }
  };

  const handleKeyPress = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      handleSend();
    }
  };

  if (loading) {
    return (
      <ChatContainer>
        <ChatHeader>
          <BackButton onClick={onBack}>
            <ArrowRight size={24} />
          </BackButton>
          <HeaderInfo>
            <Avatar
              src={match.companyLogo}
              name={match.company}
              size="sm"
            />
            <HeaderText>
              <h3>{match.company}</h3>
            </HeaderText>
          </HeaderInfo>
        </ChatHeader>
        <LoadingContainer>
          <Loader size={32} />
        </LoadingContainer>
      </ChatContainer>
    );
  }

  return (
    <ChatContainer>
      <ChatHeader>
        <BackButton onClick={onBack}>
          <ArrowRight size={24} />
        </BackButton>
        <HeaderInfo>
          <Avatar
            src={match.companyLogo}
            name={match.company}
            size="sm"
            status={match.isOnline ? 'online' : undefined}
          />
          <HeaderText>
            <h3>{match.company}</h3>
            <span>{match.isOnline ? 'מחובר/ת עכשיו' : 'לא מחובר/ת'}</span>
          </HeaderText>
        </HeaderInfo>
      </ChatHeader>

      <MessagesContainer>
        <MessagesWrapper>
          {messages
            .filter((msg) => msg.sender !== 'system')
            .map((msg) => (
              <MessageBubble key={msg.id} $isMe={msg.sender === 'me'}>
                <BubbleContent>{msg.text}</BubbleContent>
                <BubbleTime>{msg.time}</BubbleTime>
              </MessageBubble>
            ))}
          <div ref={messagesEndRef} />
        </MessagesWrapper>
      </MessagesContainer>

      <InputArea>
        <IconButton type="button">
          <Paperclip size={20} />
        </IconButton>
        <InputWrapper>
          <input
            type="text"
            value={inputText}
            onChange={(e) => setInputText(e.target.value)}
            placeholder="הקלד/י הודעה..."
            onKeyPress={handleKeyPress}
            disabled={sending}
          />
        </InputWrapper>
        <IconButton
          type="button"
          $primary
          onClick={handleSend}
          disabled={!inputText.trim() || sending}
        >
          <Send size={20} />
        </IconButton>
      </InputArea>
    </ChatContainer>
  );
};

export default ChatInterface;
