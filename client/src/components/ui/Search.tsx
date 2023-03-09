import { tags } from "@/lib/query/tags";
import { Chip, Container, Stack, TextField, Typography } from "@mui/material";
import { Link } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";
import { blue } from "@mui/material/colors";
import { useState } from "react";
import { articles } from "@/lib/query/articles";
import { useDebounce } from "react-use";
import { Else, If } from "react-if";
import ArticlePreview from "./ArticlePreview";
import SearchPreview from "./SearchPreview";

const Search: React.FC = () => {
  const [search, setSearch] = useState("");
  const [debouncedSearch, setDebouncedSearch] = useState("");
  const [, cancel] = useDebounce(
    () => {
      setDebouncedSearch(search);
    },
    500,
    [search]
  );
  const isSearching = search.length !== debouncedSearch.length;

  const { data: tagsData, isError } = useQuery(tags.keys.all, tags.queries.all);
  const {
    data: searchResults,
    isInitialLoading,
    isLoading,
  } = useQuery(
    articles.keys.bySearch(debouncedSearch),
    articles.queries.bySearch(debouncedSearch),
    {
      enabled: debouncedSearch?.length > 1,
    }
  );

  return (
    <Container sx={{ mt: 8 }}>
      <Typography variant='h3' component='h1'>
        Search
      </Typography>

      <TextField
        fullWidth
        value={search}
        onChange={e => setSearch(e.target.value)}
      />

      <If condition={isSearching}>
        <Typography>Searching...</Typography>
      </If>
      <Stack mt={2} direction='row' gap={1}>
        {tagsData?.data.map((tag, i) => (
          <Chip
            sx={{ backgroundColor: blue[500] }}
            key={tag.id}
            label={tag.name}
            component={Link}
            to={`/tags/${tag.name}`}
          />
        ))}
      </Stack>

      {searchResults?.data.map(result => (
        <SearchPreview
          articleId={result.id}
          title={result.title}
          image={result.thumbnailUrl}
        />
      ))}
      <If
        condition={
          !isSearching &&
          !isLoading &&
          debouncedSearch.length &&
          !searchResults?.data.length
        }
      >
        <Typography>No article found!</Typography>
      </If>
    </Container>
  );
};

export default Search;
