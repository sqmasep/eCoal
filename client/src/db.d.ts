export type DbId = number;

export interface User {
  id: DbId;
  name: string;
  email: string;
  email_verified_at?: string;
  role: "USER" | "ADMIN";
  password: string;
  remember_token: string;
  created_at: string;
}

export interface Tag {
  id: DbId;
  name: string;
  image: string;
  created_at: string;
  updated_at: string;
}

export type MediaType = "AUDIO" | "IMAGE" | "VIDEO";

export interface Article {
  id: DbId;
  title: string;
  content: string;
  thumbnailURL: string;
  mediaType?: MediaType;
  mediaURL?: string;
  viewCount: number;
  leadStory: boolean;
  created_at: string;
  updated_at: string;
}
